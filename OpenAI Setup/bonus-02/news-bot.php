<?php
// news-bot.php — the full news RAG loop
require __DIR__ . '/retrieve-news.php';   // Bonus 1
require __DIR__ . '/recency.php';
require __DIR__ . '/news-context.php';
require __DIR__ . '/news-prompt.php';
require __DIR__ . '/chat.php';            // Episode 6

function askNews(string $question): string {
    // 1. RETRIEVE (hybrid) + 2. RE-RANK by recency
    $chunks = applyRecency(retrieveNews($question, 6));

    // 3. Empty-retrieval guard
    if (empty($chunks)) {
        return "I don't have any recent local news about that right now.";
    }

    // 4. Build time-aware prompt
    [$context, $sources] = buildNewsContext($chunks);
    $today    = date('l, F j, Y');
    $messages = [
        ['role' => 'system', 'content' => newsSystemPrompt($context, $today)],
        ['role' => 'user',   'content' => $question],
    ];

    // 5. GENERATE
    $answer = chat($messages);

    // 6. Human-readable source list with links + timestamps
    $answer .= "\n\nSources:";
    foreach ($sources as $n => $s) {
        $answer .= "\n  [{$n}] {$s['name']} ({$s['when']}) — {$s['url']}";
    }

    return $answer;
}

$question = $argv[1] ?? 'What is happening with local trains?';
echo "Q: {$question}\n\n" . askNews($question) . "\n";
