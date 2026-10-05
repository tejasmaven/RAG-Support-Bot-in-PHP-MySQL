<?php
// ask.php — a single grounded LLM call (retrieval faked for now)

require_once __DIR__ . '/../config.php';

// This is the "context" a real retriever would fetch for us in later episodes.
$context = <<<TXT
Nimbus Refund Policy (v3):
Customers may request a full refund within 14 days of purchase.
Refunds are issued to the original payment method within 5-7 business days.
Duplicate charges are always refunded in full, regardless of the 14-day window.
TXT;

$question = "I was charged twice this month. Can I get a refund?";

$systemPrompt = <<<TXT
You are Nimbus HelpDesk AI, a support assistant.
Answer the user's question using ONLY the context provided below.
If the answer is not in the context, say you don't know and suggest contacting support.
Do not invent policies, numbers, or timeframes.

Context:
$context
TXT;

$payload = [
    'model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
    'messages' => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user',   'content' => $question],
    ],
    'temperature' => 0.2,
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
    exit('Request failed: ' . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);
echo $data['choices'][0]['message']['content'] ?? 'No answer returned.';
echo "\n";
