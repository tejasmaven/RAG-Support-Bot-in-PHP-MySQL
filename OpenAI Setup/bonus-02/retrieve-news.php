<?php
// retrieve-news.php — hybrid retrieval over the news index
require __DIR__ . '/db.php';
require __DIR__ . '/embed.php';
require __DIR__ . '/vector.php';
require __DIR__ . '/rrf.php';

function retrieveNews(string $question, int $topK = 5, float $minScore = 0.27): array {
    $queryVec = normalize(getEmbedding($question));
    $pool     = 20;

    // Vector candidates
    $v = db()->prepare(
        'SELECT id, article_url, title, source_name, published_at, content,
                dot_product(embedding, CAST(:qvec AS JSON)) AS score
           FROM news_chunks
       ORDER BY score DESC
          LIMIT :k'
    );
    $v->bindValue(':qvec', json_encode($queryVec));
    $v->bindValue(':k', $pool, PDO::PARAM_INT);
    $v->execute();
    $vec = $v->fetchAll();

    // Keyword candidates (FULLTEXT)
    $kw = db()->prepare(
        'SELECT id, article_url, title, source_name, published_at, content,
                MATCH(content) AGAINST(:q IN NATURAL LANGUAGE MODE) AS score
           FROM news_chunks
          WHERE MATCH(content) AGAINST(:q2 IN NATURAL LANGUAGE MODE)
       ORDER BY score DESC
          LIMIT :k'
    );
    $kw->bindValue(':q', $question);
    $kw->bindValue(':q2', $question);
    $kw->bindValue(':k', $pool, PDO::PARAM_INT);
    $kw->execute();
    $key = $kw->fetchAll();

    // "Nothing relevant" gate, same idea as Episode 5
    if (($vec[0]['score'] ?? 0) < $minScore && empty($key)) {
        return [];
    }

    // Reuse Reciprocal Rank Fusion from Episode 7
    $merged = reciprocalRankFusion([$vec, $key]);
    return array_slice($merged, 0, $topK);
}
