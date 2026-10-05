<?php
// chunker.php — split text into overlapping, sentence-aware chunks

function chunkText(string $text, int $maxChars = 800, int $overlap = 150): array {
    $text = trim($text);
    if ($text === '') return [];

    // Rough sentence split: break after . ! ? followed by whitespace
    $sentences = preg_split('/(?<=[.!?])\s+/', $text);

    $chunks  = [];
    $current = '';

    foreach ($sentences as $sentence) {
        $sentence = trim($sentence);
        if ($sentence === '') continue;

        // Would adding this sentence overflow the chunk? If so, close it off.
        if ($current !== '' && mb_strlen($current) + mb_strlen($sentence) + 1 > $maxChars) {
            $chunks[] = trim($current);
            // Start the next chunk with the tail of this one (the overlap)
            $current = mb_substr($current, -$overlap);
        }

        $current = $current === '' ? $sentence : $current . ' ' . $sentence;
    }

    if (trim($current) !== '') {
        $chunks[] = trim($current);
    }

    return $chunks;
}
