<?php
// search.php — let MySQL do the ranking (dot_product on normalized vectors)
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';

$question = $argv[1] ?? 'How do I get my money back?';
$topK     = 3;

// Normalize the question vector too — both sides must match
$queryVec = normalize(getEmbedding($question));

$sql = 'SELECT source, title, content,
               dot_product(embedding, CAST(:qvec AS JSON)) AS score
          FROM chunks
      ORDER BY score DESC
         LIMIT :k';

$stmt = db()->prepare($sql);
$stmt->bindValue(':qvec', json_encode($queryVec));
$stmt->bindValue(':k', $topK, PDO::PARAM_INT);
$stmt->execute();

echo "Question: $question\n\n";
foreach ($stmt->fetchAll() as $i => $row) {
    printf("#%d  score=%.3f  [%s]\n%s\n\n",
        $i + 1,
        $row['score'],
        $row['source'],
        mb_substr($row['content'], 0, 160) . '...'
    );
}
