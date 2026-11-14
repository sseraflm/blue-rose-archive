<?php
function post(string $name): string
{
    return trim($_POST[$name] ?? "");
}

function postOrNull(string $name): ?string
{
    $value = post($name);
    return $value === "" ? null : $value;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function isValidDate(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat("Y-m-d", $value);
    return $date !== false && $date->format("Y-m-d") === $value;
}

$jsonLocation = __DIR__ . "/../data/songs.json";
$errors = [];
$justSaved = isset($_GET["saved"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $decodedJson = json_decode(
        (string) @file_get_contents($jsonLocation),
        true
    );

    if (!is_array($decodedJson)) {
        $errors[] =
            "data/songs.json is missing or is not valid JSON - refusing to write.";
        $decodedJson = [];
    }

    $slug = strtolower(post("slug"));
    $title_romaji = post("title_romaji");
    $title_ja = postOrNull("title_ja");
    $title_en = postOrNull("title_en");
    $is_cover = isset($_POST["is_cover"]) ? 1 : 0;
    $album = postOrNull("album");
    $notes = postOrNull("notes");

    $artist_name = post("artist_name");
    $release_date = post("release_date");
    $youtube_url = postOrNull("youtube_url");
    $niconico_url = postOrNull("niconico_url");

    if ($artist_name === "") {
        $artist_name = "Ado";
    }

    $composer = postOrNull("composer");
    $lyricist = postOrNull("lyricist");
    $arranger = postOrNull("arranger");
    $producer = postOrNull("producer");
    $featuring = postOrNull("featuring");
    $guitar = postOrNull("guitar");
    $bass = postOrNull("bass");
    $drums = postOrNull("drums");
    $piano = postOrNull("piano");
    $mix_engineer = postOrNull("mix_engineer");
    $mastering_engineer = postOrNull("mastering_engineer");
    $programmer = postOrNull("programmer");

    $original_artist = postOrNull("original_artist");
    $original_release_date = postOrNull("original_release_date");
    $original_youtube_url = postOrNull("original_youtube_url");
    $original_niconico_url = postOrNull("original_niconico_url");

    if ($slug === "") {
        $errors[] = "Slug is required.";
    } elseif (!preg_match('/^[a-z0-9_-]+$/', $slug)) {
        $errors[] = "Slug \"{$slug}\" may only contain lowercase letters, numbers, hyphens and underscores.";
    } else {
        $existingSlugs = array_column(
            array_column($decodedJson, "songs"),
            "slug"
        );
        if (in_array($slug, $existingSlugs, true)) {
            $errors[] = "A song with the slug \"{$slug}\" already exists.";
        }
    }

    if ($title_romaji === "") {
        $errors[] = "Romaji title is required.";
    }

    if ($release_date === "") {
        $errors[] = "Release date is required.";
    } elseif (!isValidDate($release_date)) {
        $errors[] = "Release date must be a real date in YYYY-MM-DD format.";
    }

    if (
        $original_release_date !== null &&
        !isValidDate($original_release_date)
    ) {
        $errors[] =
            "Original release date must be a real date in YYYY-MM-DD format.";
    }

    if ($is_cover === 1 && $original_artist === null) {
        $errors[] = "Covers must include the original artist.";
    }

    $urls = [
        "Ado YouTube URL" => $youtube_url,
        "Ado Niconico URL" => $niconico_url,
        "Original YouTube URL" => $original_youtube_url,
        "Original Niconico URL" => $original_niconico_url,
    ];

    foreach ($urls as $label => $url) {
        if ($url !== null && filter_var($url, FILTER_VALIDATE_URL) === false) {
            $errors[] = "{$label} is not a valid URL.";
        }
    }

    $newSong = [
        "songs" => [
            "slug" => $slug,
            "title_romaji" => $title_romaji,
            "title_ja" => $title_ja,
            "title_en" => $title_en,
            "is_cover" => $is_cover,
            "album" => $album,
            "notes" => $notes,
        ],
        "song_credits" => [
            "composer" => $composer,
            "lyricist" => $lyricist,
            "arranger" => $arranger,
            "producer" => $producer,
            "featuring" => $featuring,
            "guitar" => $guitar,
            "bass" => $bass,
            "drums" => $drums,
            "piano" => $piano,
            "mix_engineer" => $mix_engineer,
            "mastering_engineer" => $mastering_engineer,
            "programmer" => $programmer,
        ],
        "song_ado_data" => [
            "artist_name" => $artist_name,
            "release_date" => $release_date,
            "youtube_url" => $youtube_url,
            "niconico_url" => $niconico_url,
        ],
        "song_original_data" => [
            "artist" => $original_artist,
            "release_date" => $original_release_date,
            "youtube_url" => $original_youtube_url,
            "niconico_url" => $original_niconico_url,
        ],
    ];

    if ($errors === []) {
        $decodedJson[] = $newSong;

        $encodedJson = json_encode(
            $decodedJson,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        $handle = fopen($jsonLocation, "c");

        if ($handle === false) {
            $errors[] = "Could not open data/songs.json for writing.";
        } elseif (!flock($handle, LOCK_EX)) {
            $errors[] = "Could not lock data/songs.json.";
            fclose($handle);
        } else {
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, $encodedJson);
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);

            header("Location: " . $_SERVER["PHP_SELF"] . "?saved=1");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>idunno</title>
  </head>
  <body>
    <?php if ($errors !== []): ?>
      <div class="form-errors">
        <p><strong>Could not save:</strong></p>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($justSaved): ?>
      <p class="form-success">Song added to data/songs.json. Run <code>php tools/sync.php</code> to push it into the database.</p>
    <?php endif; ?>

    <form action="" method="post">
      <fieldset>
        <legend>Songs</legend>
        <label for="slug">slug</label> <br/>
        <input type="text" name="slug" id="slug" /> <br/>
        <label for="title_romaji">Title romaji</label> <br/>
        <input type="text" name="title_romaji" id="title_romaji" /> <br/>
        <label for="title_ja">Japanese title</label> <br/>
        <input type="text" name="title_ja" id="title_ja" /> <br/>
        <label for="title_en">English title</label><br/>
        <input type="text" name="title_en" id="title_en" /> <br/>
        <label for="is_cover">Is cover?</label> <br/>
        <input type="checkbox" name="is_cover" id="is_cover" value="1" />
        <label for="album">Album</label> <br/>
        <input type="text" name="album" id="album" /> <br/>
        <label for="notes">Notes</label> <br/>
        <textarea name="notes" id="notes"></textarea> <br/>
      </fieldset>
      <fieldset>
        <legend>Ado's song data</legend>
        <label for="artist_name">Artist name</label><br/>
        <input type="text" name="artist_name" id="artist_name" /> <br/>
        <label for="release_date">Release date</label> <br/>
        <input type="date" name="release_date" id="release_date" /> <br/>
        <label for="youtube_url">Youtube url</label> <br/>
        <input type="text" name="youtube_url" id="youtube_url" /> <br/>
        <label for="niconico_url">niconico url</label> <br/>
        <input type="text" name="niconico_url" id="niconico_url" /> <br/>
      </fieldset>
      <fieldset>
        <legend>Song's credits.</legend>
        <label for="producer">Producer</label> <br />
        <input type="text" name="producer" id="producer" /> <br />
        <label for="composer">Composer</label> <br/>
        <input type="text" name="composer" id="composer" /> <br/>
        <label for="lyricist">Lyricist</label> <br/>
        <input type="text" id="lyricist" name="lyricist" /> <br/>
        <label for="arranger">Arranger</label> <br/>
        <input type="text" name="arranger" id="arranger" /> <br/>
        <label for="programmer">Programmer</label> <br/>
        <input type="text" name="programmer" id="programmer" /> <br/>
        <label for="mastering_engineer">Mastering engineer</label> <br/>
        <input type="text" name="mastering_engineer" id="mastering_engineer" /> <br/>
        <label for="featuring">Featuring</label> <br/>
        <input type="text" name="featuring" id="featuring" /> <br/>
        <label for="guitar">Guitar</label> <br/>
        <input type="text" name="guitar" id="guitar" /> <br/>
        <label for="bass">Bass</label> <br/>
        <input type="text" name="bass" id="bass" /> <br/>
        <label for="drums">Drums</label> <br/>
        <input type="text" name="drums" id="drums" /> <br/>
        <label for="piano">Piano / Keyboard</label> <br />
        <input type="text" name="piano" id="piano" /> <br />
        <label for="mix_engineer">Mix engineer</label> <br/>
        <input type="text" name="mix_engineer" /> <br/>
      </fieldset>
      <fieldset>
        <legend>Song's original data.</legend>
        <label for="original_artist">Original artist</label><br/>
        <input type="text" name="original_artist" id="original_artist" /> <br/>
        <label for="original_release_date">Original release date</label> <br/>
        <input type="date" name="original_release_date" id="original_release_date"/> <br/>
        <label for="original_youtube_url">Original YouTube url</label> <br/>
        <input type="text" name="original_youtube_url" id="original_youtube_url"/><br/>
        <label for="original_niconico_url">Original Niconico url</label> <br/>
        <input type="text" name="original_niconico_url" id="original_niconico_url"/><br/>
      </fieldset>
      <button type="submit">Submit</button>
    </form>
  </body>
</html>
