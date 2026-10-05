<?php
// build-index.php — embed all chunks (normalized) and store them in MySQL
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';

$chunks = json_decode(file_get_contents(__DIR__ . '/chunks.json'), true);
$pdo    = db();

$pdo->exec('TRUNCATE TABLE chunks');

$stmt = $pdo->prepare(
    'INSERT INTO chunks (source, title, chunk_index, content, embedding)
     VALUES (?, ?, ?, ?, ?)'
);

$total = count($chunks);
foreach ($chunks as $n => $c) {
    $embedding = normalize(getEmbedding($c['content']));   // normalized now
    $stmt->execute([
        $c['source'],
        $c['title'],
        $c['chunk_index'],
        $c['content'],
        json_encode($embedding),
    ]);
    echo "Embedded " . ($n + 1) . "/$total\r";
}

echo "\nDone. Stored $total chunks with embeddings.\n";
