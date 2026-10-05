<?php
// rerank.php — ask the LLM to pick the most relevant chunks (optional pass)
require __DIR__ . '/chat.php';

function rerank(string $question, array $chunks, int $keep = 3): array {
    $list = '';
    foreach ($chunks as $i => $c) {
        $list .= "[{$i}] " . mb_substr($c['content'], 0, 300) . "\n\n";
    }

    $prompt = "Question: {$question}\n\n"
        . "Passages:\n{$list}\n"
        . "Return ONLY the numbers of the {$keep} most relevant passages, "
        . "comma-separated, best first. Example: 2,0,5";

    $reply   = chat([['role' => 'user', 'content' => $prompt]], 0.0);
    $indexes = array_filter(array_map('intval', explode(',', $reply)),
                            fn($i) => isset($chunks[$i]));

    $reranked = [];
    foreach ($indexes as $i) {
        $reranked[] = $chunks[$i];
    }
    return $reranked ?: array_slice($chunks, 0, $keep);  // fallback if parsing fails
}
