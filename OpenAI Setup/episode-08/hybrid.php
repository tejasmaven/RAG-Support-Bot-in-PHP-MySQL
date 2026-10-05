<?php
// hybrid.php — keyword + vector candidate lists, fused with RRF
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';

function vectorCandidates(array $queryVec, int $limit): array {
    $stmt = db()->prepare(
        'SELECT id, source, title, content,
                dot_product(embedding, CAST(:qvec AS JSON)) AS score
           FROM chunks
       ORDER BY score DESC
          LIMIT :k'
    );
    $stmt->bindValue(':qvec', json_encode($queryVec));
    $stmt->bindValue(':k', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function keywordCandidates(string $question, int $limit): array {
    // :q and :q2 are the same text; PDO (emulation off) needs distinct names
    $stmt = db()->prepare(
        'SELECT id, source, title, content,
                MATCH(content) AGAINST(:q IN NATURAL LANGUAGE MODE) AS score
           FROM chunks
          WHERE MATCH(content) AGAINST(:q2 IN NATURAL LANGUAGE MODE)
       ORDER BY score DESC
          LIMIT :k'
    );
    $stmt->bindValue(':q',  $question);
    $stmt->bindValue(':q2', $question);
    $stmt->bindValue(':k',  $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function reciprocalRankFusion(array $lists, int $k = 60): array {
    $scores = [];
    $items  = [];

    foreach ($lists as $list) {
        foreach (array_values($list) as $rank => $row) {
            $id           = $row['id'];
            $scores[$id]  = ($scores[$id] ?? 0) + 1 / ($k + $rank + 1);
            $items[$id]   = $row;   // keep one copy of each chunk
        }
    }

    arsort($scores);  // highest fused score first

    $merged = [];
    foreach ($scores as $id => $score) {
        $row        = $items[$id];
        $row['rrf'] = $score;
        $merged[]   = $row;
    }
    return $merged;
}

function retrieveHybrid(string $question, array $options = []): array {
    $topK     = $options['top_k']     ?? 4;
    $pool     = $options['pool']      ?? 20;
    $minScore = $options['min_score'] ?? 0.27;

    $queryVec = normalize(getEmbedding($question));
    $vec      = vectorCandidates($queryVec, $pool);
    $kw       = keywordCandidates($question, $pool);

    // Quality gate: nothing semantically close AND no keyword hit -> "I don't know"
    $bestCosine = $vec[0]['score'] ?? 0;
    if ($bestCosine < $minScore && empty($kw)) {
        return [];
    }

    $merged = reciprocalRankFusion([$vec, $kw]);
    return array_slice($merged, 0, $topK);
}
