<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonLocation = __DIR__ . "/../data/songs.json";
    $jsonContents = file_get_contents($jsonLocation);
    $decodedJson = json_decode($jsonContents, true) ?? [];

    $slug = trim($_POST['slug'] ?? '');
    $title_romaji = trim($_POST['title_romaji'] ?? '');

    $title_ja = trim($_POST['title_ja'] ?? '');
    $title_ja = $title_ja === '' ? null : $title_ja;

    $title_en = trim($_POST['title_en'] ?? '');
    $title_en = $title_en === '' ? null : $title_en;

    $is_cover = isset($_POST['is_cover']) ? 1 : 0;

    $album = trim($_POST['album'] ?? '');
    $album = $album === '' ? null : $album;

    $notes = trim($_POST['notes'] ?? '');
    $notes = $notes === '' ? null : $notes; 

    $artist_name = trim($_POST['artist_name'] ?? '');
    $release_date = trim($_POST['release_date'] ?? '');

    $youtube_url = trim($_POST['youtube_url'] ?? '');
    $youtube_url = $youtube_url === '' ? null : $youtube_url;

    $niconico_url = trim($_POST['niconico_url'] ?? '');
    $niconico_url = $niconico_url === '' ? null : $niconico_url;

    $composer = trim($_POST['composer'] ?? '');
    $composer = $composer === '' ? null : $composer;

    $lyricist = trim($_POST['lyricist'] ?? '');
    $lyricist = $lyricist === '' ? null : $lyricist;
    
    $arranger = trim($_POST['arranger'] ?? '');
    $arranger = $arranger === '' ? null : $arranger;

    $producer = trim($_POST['producer'] ?? '');
    $producer = $producer === '' ? null : $producer;

    $programmer = trim($_POST['programmer'] ?? '');
    $programmer = $programmer === '' ? null : $programmer;

    $mastering_engineer = trim($_POST['mastering_engineer'] ?? '');
    $mastering_engineer = $mastering_engineer === '' ? null : $mastering_engineer;

    $featuring = trim($_POST['featuring'] ?? '');
    $featuring = $featuring === '' ? null : $featuring;

    $guitar = trim($_POST['guitar'] ?? '');
    $guitar = $guitar === '' ? null : $guitar;

    $bass = trim($_POST['bass'] ?? '');
    $bass = $bass === '' ? null : $bass;

    $drums = trim($_POST['drums'] ?? '');
    $drums = $drums === '' ? null : $drums;

    $piano = trim($_POST['piano'] ?? '');
    $piano = $piano === '' ? null : $piano;

    $mix_engineer = trim($_POST['mix_engineer'] ?? '');
    $mix_engineer = $mix_engineer === '' ? null : $mix_engineer;

    $original_artist = trim($_POST['original_artist'] ?? '');
    $original_artist = $original_artist === '' ? null : $original_artist;

    $original_release_date = trim($_POST['original_release_date'] ?? '');
    $original_release_date = $original_release_date === '' ? null : $original_release_date;

    $original_youtube_url = trim($_POST['original_youtube_url'] ?? '');
    $original_youtube_url = $original_youtube_url === '' ? null : $original_youtube_url;

    $original_niconico_url = trim($_POST['original_niconico_url'] ?? '');
    $original_niconico_url = $original_niconico_url === '' ? null : $original_niconico_url;

    $newSong = [
    'songs' => [
        'slug' => $slug,
        'title_romaji' => $title_romaji,
        'title_ja' => $title_ja,
        'title_en' => $title_en,
        'is_cover' => $is_cover,
        'album' => $album,
        'notes' => $notes
    ],
    'song_credits' => [
    'composer' => $composer,
    'lyricist' => $lyricist,
    'arranger' => $arranger,
    'producer' => $producer,
    'featuring' => $featuring,
    'guitar' => $guitar,
    'bass' => $bass,
    'drums' => $drums,
    'piano' => $piano,
    'mix_engineer' => $mix_engineer,
    'mastering_engineer' => $mastering_engineer,
    'programmer' => $programmer
    ],
    'song_ado_data' => [
        'artist_name' => $artist_name,
        'release_date' => $release_date,
        'youtube_url' => $youtube_url,
        'niconico_url' => $niconico_url
    ],
    'song_original_data' => [
        'artist' => $original_artist,
        'release_date' => $original_release_date,
        'youtube_url' => $original_youtube_url,
        'niconico_url' => $original_niconico_url
    ]
];
$decodedJson[] = $newSong;

$encodedJson = json_encode($decodedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES |JSON_UNESCAPED_UNICODE);
$file = fopen($jsonLocation, 'c');
flock($file, LOCK_EX);
fwrite($file, $encodedJson);
flock($file, LOCK_UN);
fclose($file);
header('Location: ' . $_SERVER['PHP_SELF']);
exit;
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
