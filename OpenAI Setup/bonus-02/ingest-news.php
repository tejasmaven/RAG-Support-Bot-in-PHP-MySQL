<?php
// ingest-news.php — fetch, chunk, embed, and store news (dedup-safe)
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';
require __DIR__ . '/chunker.php';       // Episode 2, unchanged
require __DIR__ . '/news-source.php';

$topics   = $argv[1] ?? 'Mumbai';        // your city / topic
$articles = fetchNews($topics, 'in', 10);

$pdo  = db();
$stmt = $pdo->prepare(
    'INSERT IGNORE INTO news_chunks
       (article_url, title, source_name, published_at, chunk_index, content, embedding)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);

$stored = $skipped = 0;

foreach ($articles as $a) {
    if ($a['url'] === '' || $a['title'] === '') {
        continue;
    }

    $text = $a['title'];
    if ($a['description'] !== '') $text .= ". " . $a['description'];
    if ($a['content'] !== '')     $text .= "\n\n" . $a['content'];

    $chunks = chunkText($text, 800, 150);

    foreach ($chunks as $i => $chunk) {
        $embedding = normalize(getEmbedding($chunk));
        $stmt->execute([
            $a['url'], $a['title'], $a['source_name'],
            $a['published_at'], $i, $chunk, json_encode($embedding),
        ]);
        $stmt->rowCount() > 0 ? $stored++ : $skipped++;
    }
}

echo "Stored {$stored} new chunks, skipped {$skipped} duplicates.\n";
