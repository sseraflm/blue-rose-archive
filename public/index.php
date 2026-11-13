<?php
require __DIR__ . "/../src/db.php";
$sql = "SELECT * FROM songs";


$sql = "SELECT s.slug, s.title_romaji, s.title_ja, s.title_en, sa.release_date  FROM songs AS s JOIN song_ado_data AS sa ON s.id = sa.song_id ORDER BY sa.release_date DESC;";
$results = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


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
    <main>
      <p>The catalog</p>
      <p><?= "Songs in the catalog: " . count($results) ?></p>
      <hr>
      <div id="all_song_list">
        <?php foreach ($results as $song): ?>
        <div class="song-item">
          <a href="/songs/<?= htmlspecialchars($song['slug']) ?>">
            <p><?= htmlspecialchars($song['title_romaji'])?></p>
            <?php if ($song['title_romaji'] !== $song['title_en']): ?> 
              <p><?= $song['title_en']?></p>
              <?php endif ?>
            <?php if ($song['title_ja']): ?>
            <p><?= htmlspecialchars($song['title_ja']) ?></p>
            <?php endif; ?>
            <p><?= $song['release_date'] ?></p>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
    <script src="js/search.js"></script>
  </body>

</html>