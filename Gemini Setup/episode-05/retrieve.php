<?php
// retrieve.php — turn a question into the best available context
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';

function retrieve(string $question, array $options = []): array {
    $topK          = $options['top_k']          ?? 4;
    $minScore      = $options['min_score']      ?? 0.27;
    $maxPerSource  = $options['max_per_source'] ?? 2;
    $maxChars      = $options['max_chars']      ?? 4000;
    $docType       = $options['doc_type']       ?? null;

    $queryVec = normalize(getEmbedding($question));

    // Fetch a few extra candidates so diversity capping has room to work
    $sql = 'SELECT source, title, content,
                   dot_product(embedding, CAST(:qvec AS JSON)) AS score
              FROM chunks';
    if ($docType !== null) {
        $sql .= ' WHERE doc_type = :doc_type';
    }
    $sql .= ' HAVING score >= :min_score
              ORDER BY score DESC
              LIMIT :k';

    $stmt = db()->prepare($sql);
    $stmt->bindValue(':qvec', json_encode($queryVec));
    $stmt->bindValue(':min_score', $minScore);
    $stmt->bindValue(':k', $topK * 3, PDO::PARAM_INT);
    if ($docType !== null) {
        $stmt->bindValue(':doc_type', $docType);
    }
    $stmt->execute();

    $results   = [];
    $perSource = [];
    $charsUsed = 0;

    foreach ($stmt->fetchAll() as $row) {
        $src = $row['source'];

        // Diversity: don't let one document dominate
        if (($perSource[$src] ?? 0) >= $maxPerSource) {
            continue;
        }
        // Context budget: stop before the prompt gets bloated
        if ($charsUsed + mb_strlen($row['content']) > $maxChars) {
            continue;
        }

        $results[] = [
            'source'  => $src,
            'title'   => $row['title'],
            'content' => $row['content'],
            'score'   => round((float) $row['score'], 4),
        ];

        $perSource[$src] = ($perSource[$src] ?? 0) + 1;
        $charsUsed      += mb_strlen($row['content']);

        if (count($results) >= $topK) {
            break;
        }
    }

    return $results; // empty array = nothing relevant found
}
