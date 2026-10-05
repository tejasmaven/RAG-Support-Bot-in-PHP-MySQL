<?php
// api.php — HTTP endpoint for the bot
header('Content-Type: application/json');
require __DIR__ . '/bot.php';

$question = trim($_POST['question'] ?? '');

if ($question === '' || mb_strlen($question) > 500) {
    http_response_code(400);
    echo json_encode(['error' => 'Please ask a question (1-500 characters).']);
    exit;
}

try {
    echo json_encode(['answer' => ask($question)]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Something went wrong. Please try again.']);
    // Log $e->getMessage() server-side — never show it to the user.
}
