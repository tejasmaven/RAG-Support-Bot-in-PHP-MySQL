<?php
// ask.php — full loop using hybrid retrieval + hardened prompt
require __DIR__ . '/hybrid.php';
require __DIR__ . '/context.php';
require __DIR__ . '/prompt.php';
require __DIR__ . '/chat.php';

function ask(string $question): string {
    $chunks = retrieveHybrid($question);

    if (empty($chunks)) {
        return "I don't have that information in our help center. "
             . "Please contact support@nimbus.example and our team will help you.";
    }

    [$context, $sources] = buildContext($chunks);
    $messages = [
        ['role' => 'system', 'content' => systemPrompt($context)],
        ['role' => 'user',   'content' => $question],
    ];

    $answer = chat($messages);

    $answer .= "\n\nSources:";
    foreach (array_unique($sources) as $n => $file) {
        $answer .= "\n  [{$n}] {$file}";
    }

    return $answer;
}

$question = $argv[1] ?? 'How do I get my money back?';
echo "Q: {$question}\n\n";
echo ask($question) . "\n";
