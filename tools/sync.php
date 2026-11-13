<?php
if (php_sapi_name() !== "cli") {
    exit();
}
require __DIR__ . "/../src/db.php";

$jsonLocation = __DIR__ . "/../data/songs.json";
$jsonContents = file_get_contents($jsonLocation);
$decodedJson = json_decode($jsonContents, true) ?? [];

try {
    $pdo->beginTransaction();

    $existingSongs = $pdo->query("SELECT `slug`, `id` FROM `songs`")->fetchAll(PDO::FETCH_KEY_PAIR);

    $insertSongStmt = $pdo->prepare(
        "INSERT INTO `songs` (`slug`, `title_romaji`, `title_ja`, `title_en`, `is_cover`, `album`, `notes`) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    $updateSongStmt = $pdo->prepare(
        "UPDATE `songs` 
         SET `title_romaji` = ?, 
             `title_ja` = ?, 
             `title_en` = ?, 
             `is_cover` = ?, 
             `album` = ?, 
             `notes` = ?
         WHERE `id` = ?"
    );

    $creditsStmt = $pdo->prepare(
        "INSERT INTO `song_credits` (`song_id`, `composer`, `lyricist`, `arranger`, `producer`, `featuring`, `guitar`, `bass`, `drums`, `piano`, `mix_engineer`, `mastering_engineer`, `programmer`) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE 
             `composer` = VALUES(`composer`), 
             `lyricist` = VALUES(`lyricist`), 
             `arranger` = VALUES(`arranger`),
             `producer` = VALUES(`producer`),
             `featuring` = VALUES(`featuring`), 
             `guitar` = VALUES(`guitar`), 
             `bass` = VALUES(`bass`), 
             `drums` = VALUES(`drums`),
             `piano` = VALUES(`piano`),
             `mix_engineer` = VALUES(`mix_engineer`),
             `mastering_engineer` = VALUES(`mastering_engineer`),
             `programmer` = VALUES(`programmer`)"
    );

    $adoStmt = $pdo->prepare(
        "INSERT INTO `song_ado_data` (`song_id`, `artist_name`, `release_date`, `youtube_url`, `niconico_url`) 
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE 
             `artist_name` = VALUES(`artist_name`), 
             `release_date` = VALUES(`release_date`), 
             `youtube_url` = VALUES(`youtube_url`), 
             `niconico_url` = VALUES(`niconico_url`)"
    );

    $originalStmt = $pdo->prepare(
        "INSERT INTO `song_original_data` (`song_id`, `artist`, `release_date`, `youtube_url`, `niconico_url`) 
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE 
             `artist` = VALUES(`artist`), 
             `release_date` = VALUES(`release_date`), 
             `youtube_url` = VALUES(`youtube_url`), 
             `niconico_url` = VALUES(`niconico_url`)"
    );

    foreach ($decodedJson as $song) {
        $slug = $song["songs"]["slug"];

        if (isset($existingSongs[$slug])) {
            $songId = (int) $existingSongs[$slug];
            $updateSongStmt->execute([
                $song["songs"]["title_romaji"],
                $song["songs"]["title_ja"],
                $song["songs"]["title_en"],
                $song["songs"]["is_cover"],
                $song["songs"]["album"],
                $song["songs"]["notes"],
                $songId,
            ]);
        } else {
            $insertSongStmt->execute([
                $slug,
                $song["songs"]["title_romaji"],
                $song["songs"]["title_ja"],
                $song["songs"]["title_en"],
                $song["songs"]["is_cover"],
                $song["songs"]["album"],
                $song["songs"]["notes"],
            ]);
            $songId = (int) $pdo->lastInsertId();
            $existingSongs[$slug] = $songId;
        }

        $creditsStmt->execute([
            $songId,
            $song["song_credits"]["composer"],
            $song["song_credits"]["lyricist"],
            $song["song_credits"]["arranger"],
            $song["song_credits"]["producer"],
            $song["song_credits"]["featuring"],
            $song["song_credits"]["guitar"],
            $song["song_credits"]["bass"],
            $song["song_credits"]["drums"],
            $song["song_credits"]["piano"],
            $song["song_credits"]["mix_engineer"],
            $song["song_credits"]["mastering_engineer"],
            $song["song_credits"]["programmer"],
        ]);

        $adoStmt->execute([
            $songId,
            $song["song_ado_data"]["artist_name"],
            $song["song_ado_data"]["release_date"],
            $song["song_ado_data"]["youtube_url"],
            $song["song_ado_data"]["niconico_url"],
        ]);

        if ($song["songs"]["is_cover"] === 1) {
            $originalStmt->execute([
                $songId,
                $song["song_original_data"]["artist"],
                $song["song_original_data"]["release_date"],
                $song["song_original_data"]["youtube_url"],
                $song["song_original_data"]["niconico_url"],
            ]);
        }
    }

    $pdo->commit();
    echo "Completed.";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Failed: " . $e->getMessage();
}
?>