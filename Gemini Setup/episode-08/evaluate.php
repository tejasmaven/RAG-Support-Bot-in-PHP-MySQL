<?php
// evaluate.php — score the bot against the eval set
require __DIR__ . '/bot.php';
require __DIR__ . '/chat.php';

function judge(string $question, string $answer, string $context): int {
    $prompt = "Grade this support answer from 1 to 5.\n"
        . "5 = fully correct and supported by the reference; 1 = wrong or unsupported.\n\n"
        . "Question: {$question}\n\nAnswer: {$answer}\n\n"
        . "Reference context:\n{$context}\n\n"
        . "Reply with ONLY the number.";
    $reply = chat([['role' => 'user', 'content' => $prompt]], 0.0);
    return (int) filter_var($reply, FILTER_SANITIZE_NUMBER_INT);
}

$cases = json_decode(file_get_contents(__DIR__ . '/eval-set.json'), true);

$hits = $refusalOK = 0;
$qualityScores = [];
$answerable = 0;

foreach ($cases as $c) {
    $chunks   = retrieveHybrid($c['question']);
    $sources  = array_column($chunks, 'source');
    $answer   = ask($c['question']);
    $refused  = str_contains($answer, "I don't have that information");

    if ($c['expect_source'] && in_array($c['expect_source'], $sources, true)) {
        $hits++;
    }
    if ($refused === !$c['should_answer']) {
        $refusalOK++;
    }
    if ($c['should_answer']) {
        $answerable++;
        $context = implode("\n", array_column($chunks, 'content'));
        $qualityScores[] = judge($c['question'], $answer, $context);
    }

    $status = $refused ? 'REFUSED' : 'answered';
    printf("[%s] %s\n", $status, $c['question']);
}

$n = count($cases);
printf("\n- Results over %d cases -\n", $n);
printf("Retrieval hit rate : %d/%d\n", $hits, $answerable);
printf("Refusal accuracy   : %d/%d\n", $refusalOK, $n);
printf("Avg answer quality : %.2f / 5\n",
    $qualityScores ? array_sum($qualityScores) / count($qualityScores) : 0);
