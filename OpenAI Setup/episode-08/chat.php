<?php
// chat.php — send messages to the chat API, return the reply text
require_once __DIR__ . '/../config.php';

function chat(array $messages, float $temperature = 0.2): string {
    $payload = [
        'model'       => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
        'messages'    => $messages,
        'temperature' => $temperature,
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
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
        throw new RuntimeException('Chat request failed: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return trim($data['choices'][0]['message']['content'] ?? '');
}
