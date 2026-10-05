<?php
// chat.php — send messages to Google Gemini, return the reply text.
// Same signature as the OpenAI version, so the rest of the code is unchanged.
require_once __DIR__ . '/../config.php';

function chat(array $messages, float $temperature = 0.2): string {
    $model = env('GEMINI_CHAT_MODEL', 'gemini-3.8-flash');
    $url   = 'https://generativelanguage.googleapis.com/v1beta/models/'
           . $model . ':generateContent';

    // Map OpenAI-style messages onto Gemini's shape:
    //   system   -> system_instruction
    //   user      -> contents[] role "user"
    //   assistant -> contents[] role "model"
    $system   = '';
    $contents = [];
    foreach ($messages as $m) {
        if ($m['role'] === 'system') {
            $system .= ($system === '' ? '' : "\n") . $m['content'];
            continue;
        }
        $role = $m['role'] === 'assistant' ? 'model' : 'user';
        $contents[] = ['role' => $role, 'parts' => [['text' => $m['content']]]];
    }

    $generationConfig = ['temperature' => $temperature];
    // Gemini 2.5+ Flash/Pro are "thinking" models: reasoning tokens are counted
    // against the output budget and can leave nothing for the visible reply.
    // Turn thinking off for direct, grounded answers.
    if (preg_match('/2\.5|3\./', $model)) {
        $generationConfig['thinkingConfig'] = ['thinkingBudget' => 0];
    }

    $payload = ['contents' => $contents, 'generationConfig' => $generationConfig];
    if ($system !== '') {
        $payload['system_instruction'] = ['parts' => [['text' => $system]]];
    }

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
        throw new RuntimeException('Chat request failed: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    if ($text === null) {
        // Surface the real API error instead of failing silently.
        throw new RuntimeException('Gemini returned no text. Response: ' . $response);
    }
    return trim($text);
}
