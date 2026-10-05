<?php
// embed.php — turn a string into an embedding vector (Google Gemini)
require_once __DIR__ . '/../config.php';

function getEmbedding(string $text): array {
    $model = env('GEMINI_EMBED_MODEL', 'gemini-embedding-001');
    $url   = 'https://generativelanguage.googleapis.com/v1beta/models/'
           . $model . ':embedContent';

    $payload = [
        'model'                => 'models/' . $model,
        'content'              => ['parts' => [['text' => $text]]],
        // Truncate to a smaller, cheaper-to-store vector. Both sides use the
        // same call, so document and query embeddings stay comparable.
        'outputDimensionality' => (int) env('GEMINI_EMBED_DIM', '768'),
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-goog-api-key: ' . env('GEMINI_API_KEY'),
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        throw new RuntimeException('Embedding request failed: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['embedding']['values']
        ?? throw new RuntimeException('No embedding returned: ' . $response);
}
