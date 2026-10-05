<?php
// test-retrieve.php
require __DIR__ . '/retrieve.php';

$question = $argv[1] ?? 'How do I get my money back?';
$chunks   = retrieve($question);

echo "Question: $question\n\n";

if (empty($chunks)) {
    echo "No relevant information found.\n";
    exit;
}

foreach ($chunks as $i => $c) {
    printf("#%d  score=%.3f  [%s]\n%s\n\n",
        $i + 1, $c['score'], $c['source'],
        mb_substr($c['content'], 0, 140) . '...'
    );
}
