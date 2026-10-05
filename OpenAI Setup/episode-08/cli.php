<?php
// cli.php — run the bot from the command line
require __DIR__ . '/bot.php';

$question = $argv[1] ?? 'How do I get my money back?';
echo "Q: {$question}\n\n" . ask($question) . "\n";
