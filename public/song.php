<?php
require __DIR__ . "/../src/db.php";

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$slug = $_GET['slug'] ?? '';
$sql = "SELECT * FROM songs WHERE slug = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$slug]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$song) {
    http_response_code(404);
    die("Song not found.");
}

$songId = $song['id'];

$creditsStmt = $pdo->prepare("SELECT * FROM song_credits WHERE song_id = ?");
$creditsStmt->execute([$songId]);
$credits = $creditsStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$adoStmt = $pdo->prepare("SELECT * FROM song_ado_data WHERE song_id = ?");
$adoStmt->execute([$songId]);
$adoData = $adoStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$originalData = [];
if ($song['is_cover'] == 1) {
    $origStmt = $pdo->prepare("SELECT * FROM song_original_data WHERE song_id = ?");
    $origStmt->execute([$songId]);
    $originalData = $origStmt->fetch(PDO::FETCH_ASSOC) ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blue Rose Archive</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
  </head>

  <body>
    <header>
      <div class="header-left">
        <a href="index.php" class="links">Blue Rose Archive</a>
      </div>
      
      <div class="header-center">
        <input type="text" id="search-bar" placeholder="Search songs...">
        <div id="search-results"></div>
      </div>
    </header>
    <hr>

    <div id="mid">
      <div id="isCover">
        <p>Title in romaji: <?= e($song['title_romaji'] ?? '-') ?></p>
        <p>Title in japanese: <?= e($song['title_ja'] ?? '-') ?></p>
        <p>Title in english: <?= e($song['title_en'] ?? '-') ?></p>

        <p><?= $song['is_cover'] ? 'This song is a cover.' : 'This song is an original' ?></p>
        <p>Album: <?= e($song['album'] ?? '-') ?></p>
      </div>
    </div>
    <hr>
    <main id="song-main">
      <div id="credits" class="song-card">
        <p>Song credits</p>
        <p>Composer: <?= e($credits['composer'] ?? '-') ?></p>
        <p>Lyricist: <?= e($credits['lyricist'] ?? '-') ?></p>
        <p>Arranger: <?= e($credits['arranger'] ?? '-') ?></p>
        <p>Producer: <?= e($credits['producer'] ?? '-') ?></p>
        <p>Featuring: <?= e($credits['featuring'] ?? '-') ?></p>
        <p>Guitar: <?= e($credits['guitar'] ?? '-') ?></p>
        <p>Bass: <?= e($credits['bass'] ?? '-') ?></p>
        <p>Drums: <?= e($credits['drums'] ?? '-') ?></p>
        <p>Piano: <?= e($credits['piano'] ?? '-') ?></p>
        <p>Mix Engineer: <?= e($credits['mix_engineer'] ?? '-') ?></p>
        <p>Mastering Engineer: <?= e($credits['mastering_engineer'] ?? '-') ?></p>
        <p>Programmer: <?= e($credits['programmer'] ?? '-') ?></p>
      </div>
      <div id="ado_data" class="song-card">
        <p>Ado's data</p>
        <p>Artist name: <?= e($adoData['artist_name'] ?? '-') ?></p>
        <p>Song's release date: <?= e($adoData['release_date'] ?? '-') ?></p>
        <?php if (!empty($adoData['youtube_url'])): ?>
          <p>Youtube link: <a href="<?= e($adoData['youtube_url']) ?>" target="_blank" rel="noopener noreferrer">Click here to go to YouTube.</a></p>
        <?php else: ?>
          <p>Youtube link: -</p>
        <?php endif; ?>

        <?php if (!empty($adoData['niconico_url'])): ?>
          <p>Niconico link: <a href="<?= e($adoData['niconico_url']) ?>" target="_blank" rel="noopener noreferrer">Click here to go to Niconico.</a></p>
        <?php else: ?>
          <p>Niconico link: -</p>
        <?php endif; ?>
        <p>Notes: <?= e($song['notes'] ?? "-") ?></p>
      </div>
      <div id="original_data" class="song-card">
        <p>Original data</p>
        <p>Original artist: <?= e($originalData['artist'] ?? '-') ?></p>
        <p>Release date: <?= e($originalData['release_date'] ?? '-') ?></p>
        <?php if (!empty($originalData['youtube_url'])): ?>
          <p>Youtube link: <a href="<?= e($originalData['youtube_url']) ?>" target="_blank" rel="noopener noreferrer">Click here to go to YouTube.</a></p>
        <?php else: ?>
          <p>Youtube link: -</p>
        <?php endif; ?>

        <?php if (!empty($originalData['niconico_url'])): ?>
          <p>Niconico link: <a href="<?= e($originalData['niconico_url']) ?>" target="_blank" rel="noopener noreferrer">Click here to go to Niconico.</a></p>
        <?php else: ?>
          <p>Niconico link: -</p>
        <?php endif; ?>
      </div>
    </main>
    <script src="js/search.js"></script>
  </body>

</html>