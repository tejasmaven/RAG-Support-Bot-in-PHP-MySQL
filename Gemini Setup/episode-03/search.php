<?php
// search.php — find the chunks closest in meaning to a question (cosine in PHP)
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';

function cosineSimilarity(array $a, array $b): float {
    $dot = $normA = $normB = 0.0;
    foreach ($a as $i => $v) {
        $dot   += $v * $b[$i];
        $normA += $v * $v;
        $normB += $b[$i] * $b[$i];
    }
    return $dot / (sqrt($normA) * sqrt($normB) + 1e-10);
}

$question = $argv[1] ?? 'How do I get my money back?';
$queryVec = getEmbedding($question);

$rows   = db()->query('SELECT source, content, embedding FROM chunks')->fetchAll();
$scored = [];

foreach ($rows as $row) {
    $vec      = json_decode($row['embedding'], true);
    $scored[] = ['score' => cosineSimilarity($queryVec, $vec), 'row' => $row];
}

usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

echo "Question: $question\n\n";
foreach (array_slice($scored, 0, 3) as $i => $s) {
    printf("#%d  score=%.3f  [%s]\n%s\n\n",
        $i + 1,
        $s['score'],
        $s['row']['source'],
        mb_substr($s['row']['content'], 0, 160) . '...'
    );
}
