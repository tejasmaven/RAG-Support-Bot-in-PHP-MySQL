<?php
// calibrate.php — find a sensible threshold for YOUR content
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';

$shouldMatch = [
    'How do I get my money back?',
    'I was charged twice',
    'How long do refunds take?',
    'How do I create an account?',
];

$shouldNotMatch = [
    "What's the weather in Mumbai today?",
    'Who won the cricket match last night?',
    'Can you write me a poem about the sea?',
];

function topScore(string $question): float {
    $vec  = normalize(getEmbedding($question));
    $stmt = db()->prepare(
        'SELECT dot_product(embedding, CAST(:qvec AS JSON)) AS score
           FROM chunks
       ORDER BY score DESC
          LIMIT 1'
    );
    $stmt->bindValue(':qvec', json_encode($vec));
    $stmt->execute();
    return (float) ($stmt->fetchColumn() ?: 0);
}

echo "SHOULD match (relevant questions)\n";
$good = [];
foreach ($shouldMatch as $q) {
    $s = topScore($q);
    $good[] = $s;
    printf("  %.3f  %s\n", $s, $q);
}

echo "\nSHOULD NOT match (irrelevant questions)\n";
$bad = [];
foreach ($shouldNotMatch as $q) {
    $s = topScore($q);
    $bad[] = $s;
    printf("  %.3f  %s\n", $s, $q);
}

printf("\nLowest good score : %.3f\n", min($good));
printf("Highest junk score: %.3f\n", max($bad));
printf("-> Set your threshold between these two, e.g. %.2f\n",
    (min($good) + max($bad)) / 2);
