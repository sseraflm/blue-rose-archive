# Blue Rose Archive

## About

This project is an open-source discography and credit archive for Ado's original songs and covers.

If a song is a cover, it stores links to both the original and Ado's version alongside Ado's version credits.

## How it works

The project has two layers: a source of truth you edit by hand, and a database that serves the site.

- Adding a song is done through `tools/add-song.php`, a local-only form. Submitting it appends one entry to `data/songs.json`.
- `php tools/sync.php` reads that JSON and upserts it into MariaDB, matching songs by `slug`. It is safe to run as often as you like: existing rows are updated, new ones inserted.
- The public pages read only from the database. `public/index.php` lists the catalogue, `public/song.php` renders a single song with its credits and links, and `public/api/search.php` backs the live search box.

### Data model

`songs` holds what every song has: slug, the romaji/Japanese/English titles, `is_cover`, album and notes.

Each song has at most one row in each of these three tables, linked back to it by `song_id` in a one-to-one relationship. A row cannot exist without its song, so deleting a song deletes its rows as well.

| Table | Holds |
| :--- | :--- |
| `song_credits` | composer, lyricist, arranger, producer, performers and engineers |
| `song_ado_data` | Ado's release date, YouTube link, Niconico link |
| `song_original_data` | for covers only: the original artist, its release date and links |

Keeping them separate leaves `songs` small, and makes "is this a cover?" a matter of whether a `song_original_data` row exists.

## Architecture

Only `public/` is exposed as the Nginx document root, keeping configuration, database logic and CLI scripts outside the web root. Clean song URLs `/songs/<slug>` are rewritten internally by Nginx to `/song.php?slug=<slug>`.

```text
├── config/     # config.php, copied from the example config but filled with real data.
├── data/       # songs.json, the dataset that holds songs information and can be edited by hand.
├── database/   # schema.sql, the database dump ready to import.
├── public/     # web root, files in there get served by Nginx.
├── src/        # db.php, shared PDO connection.     
└── tools/      # add-song.php is a local form that writes into JSON, and sync.php imports that JSON into the database.
```

## Technologies

- Backend - PHP 8.x
- Database - MariaDB
- Frontend - HTML5, CSS3, Vanilla JavaScript
- Web Server - Nginx + PHP-FPM

## Data Progress

| Category | Release | Status | Count |
| :--- | :--- | :--- | :--- |
| Album | Kyōgen | In Progress | 14/14 |
| Album | Uta no Uta | Planned | 088 |
| Album | Zanmu | Planned | 0/16 | 
| Album | Ado's Utattemita Album | Planned | 0/10 |
| Album | Ado's Utattemita Album Vol. 2 | Not released yet | ?/? |
| Album | Ado's Best Adobum Disc 1| Planned | 10/20 |
| Album | Ado's Best Adobum Disc 2 | Planned | 7/20 |
| Singles | Standalone Single Releases | Planned | 0/23 |
| Covers | Youtube / Niconico Covers | Planned | 1/112? |
| Features | Songs where Ado is featured | Planned | 0/28 |
| Extras | Special songs | Planned | 0/13 |

## Setup

This guide assumes a Linux environment.

1. Clone the repo:

```bash
git clone https://github.com/sseraflm/blue-rose-archive.git
cd blue-rose-archive
```

2. Create the MariaDB database, user, and import the schema (replace `your_password` with your own):
```bash
sudo mariadb -e "CREATE DATABASE blue_rose_archive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mariadb -e "CREATE USER 'bluerose'@'localhost' IDENTIFIED BY 'your_password'; GRANT ALL PRIVILEGES ON blue_rose_archive.* TO 'bluerose'@'localhost';"
sudo mariadb blue_rose_archive < database/schema.sql
```

3. Configure database credentials:
```bash
cp config/config.example.php config/config.php
```
Edit `config/config.php` with your database host, port, name, username, and password.

4. Sync song data into the database:
```bash
php tools/sync.php
```

5. Configure Nginx:
Point your server block `root` to `public/` and add the rewrite rule for clean song URLs:

Adjust the `fastcgi_pass` socket and `include` path for your distro:

```nginx
root /path/to/blue-rose-archive/public;
index index.php;

rewrite ^/songs/([a-zA-Z0-9_-]+)$ /song.php?slug=$1 last;

location / {
    try_files $uri $uri/ =404;
}

location ~ \.php$ {
    try_files $uri =404;
    fastcgi_pass unix:/run/php-fpm/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi.conf;
}
```

## License

This project is licensed under the GNU Affero General Public License v3.0 (AGPLv3). Any modified version hosted publicly over a network must also make its source code available under the same license.