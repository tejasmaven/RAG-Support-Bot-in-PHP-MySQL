<?php
// ask.php — the complete RAG loop
require __DIR__ . '/retrieve.php';   // from Episode 5
require __DIR__ . '/context.php';
require __DIR__ . '/prompt.php';
require __DIR__ . '/chat.php';

function ask(string $question): string {
    // 1. RETRIEVE
    $chunks = retrieve($question);

    // 2. The critical guard: nothing relevant -> don't even call the model
    if (empty($chunks)) {
        return "I don't have that information in our help center. "
             . "Please contact support@nimbus.example and our team will help you.";
    }

    // 3. AUGMENT — build context + prompt
    [$context, $sources] = buildContext($chunks);
    $messages = [
        ['role' => 'system', 'content' => systemPrompt($context)],
        ['role' => 'user',   'content' => $question],
    ];

    // 4. GENERATE
    $answer = chat($messages);

    // 5. Append a human-readable source list
    $answer .= "\n\nSources:";
    foreach (array_unique($sources) as $n => $file) {
        $answer .= "\n  [{$n}] {$file}";
    }

    return $answer;
}

// CLI usage
$question = $argv[1] ?? 'How do I get my money back?';
echo "Q: {$question}\n\n";
echo ask($question) . "\n";
