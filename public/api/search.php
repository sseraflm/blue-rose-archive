<?php


require __DIR__ . "/../../src/db.php";


$query = trim($_GET["q"] ?? "");

if ($query === "") {
    echo json_encode([]);
    exit;
}

$search = "%" . $query . "%";

$sql = "SELECT `slug`, `title_romaji`, `title_en`, `title_ja`
        FROM `songs`
        WHERE `title_romaji` LIKE ?
           OR `title_en` LIKE ?
           OR `title_ja` LIKE ?
        LIMIT 5";

$stmt = $pdo->prepare($sql);

$stmt->execute([$search,$search,$search]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results, JSON_UNESCAPED_UNICODE);