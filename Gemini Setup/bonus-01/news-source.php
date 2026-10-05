<?php
// news-source.php — fetch news and normalize it to our standard shape
require_once __DIR__ . '/../config.php';

function httpGetJson(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $body = curl_exec($ch);
    if ($body === false) {
        throw new RuntimeException('Fetch failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($body, true) ?? [];
}

// Fetch raw articles from GNews for a topic/place
function fetchNews(string $query, string $country = 'in', int $max = 10): array {
    $url = 'https://gnews.io/api/v4/search?' . http_build_query([
        'q'       => $query,
        'country' => $country,     // e.g. 'in', 'us', 'gb'
        'lang'    => 'en',
        'max'     => $max,
        'apikey'  => env('GNEWS_API_KEY'),
    ]);

    $data = httpGetJson($url);
    return array_map('normalizeArticle', $data['articles'] ?? []);
}

// THE ADAPTER: GNews shape -> our standard shape.
// Swap providers by rewriting only this function.
function normalizeArticle(array $a): array {
    return [
        'url'          => $a['url']                 ?? '',
        'title'        => trim($a['title']          ?? ''),
        'description'  => trim($a['description']    ?? ''),
        'content'      => trim($a['content']        ?? ''),
        'source_name'  => $a['source']['name']      ?? 'Unknown',
        'published_at' => isset($a['publishedAt'])
            ? date('Y-m-d H:i:s', strtotime($a['publishedAt']))
            : null,
    ];
}
