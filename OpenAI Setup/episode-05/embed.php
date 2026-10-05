<?php
// embed.php — turn a string into an embedding vector
require_once __DIR__ . '/../config.php';

function getEmbedding(string $text): array {
    $payload = [
        'model' => env('OPENAI_EMBED_MODEL', 'text-embedding-3-small'),
        'input' => $text,
    ];

    $ch = curl_init('https://api.openai.com/v1/embeddings');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('OPENAI_API_KEY'),
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        throw new RuntimeException('Embedding request failed: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['data'][0]['embedding']
        ?? throw new RuntimeException('No embedding returned: ' . $response);
}
