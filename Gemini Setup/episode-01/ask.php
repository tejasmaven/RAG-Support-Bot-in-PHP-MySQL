<?php
// ask.php — a single grounded LLM call (retrieval faked for now) — Google Gemini

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

$model = env('GEMINI_CHAT_MODEL', 'gemini-3.6-flash');
$url   = 'https://generativelanguage.googleapis.com/v1beta/models/'
       . $model . ':generateContent';

$generationConfig = ['temperature' => 0.2];
// Gemini 2.5+ Flash/Pro are "thinking" models: reasoning tokens are counted
// against the output budget and can leave zero tokens for the actual reply.
// A support bot doesn't need deep reasoning, so turn thinking off.
if (preg_match('/2\.5|3\./', $model)) {
    $generationConfig['thinkingConfig'] = ['thinkingBudget' => 0];
}

$payload = [
    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
    'contents'           => [['role' => 'user', 'parts' => [['text' => $question]]]],
    'generationConfig'   => $generationConfig,
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
    exit('Request failed: ' . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);
$text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

if ($text === null) {
    // Surface the real problem (bad key, wrong model, safety block, etc.)
    echo "No answer returned.\nRaw API response:\n" . $response . "\n";
} else {
    echo $text . "\n";
}
