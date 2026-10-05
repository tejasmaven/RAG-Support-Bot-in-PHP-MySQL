<?php
// context.php — format retrieved chunks into a numbered context block
function buildContext(array $chunks): array {
    $blocks  = [];
    $sources = [];

    foreach ($chunks as $i => $chunk) {
        $n = $i + 1;
        $blocks[]     = "[{$n}] Source: {$chunk['source']}\n{$chunk['content']}";
        $sources[$n]  = $chunk['source'];   // remember the number -> file mapping
    }

    return [implode("\n\n---\n\n", $blocks), $sources];
}
