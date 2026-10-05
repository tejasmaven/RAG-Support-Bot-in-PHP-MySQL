<?php
// ingest.php — load every doc, chunk it, save chunks.json

require __DIR__ . '/loader.php';
require __DIR__ . '/chunker.php';

$kbDir  = __DIR__ . '/knowledge-base';
$files  = glob($kbDir . '/*.{md,txt,pdf}', GLOB_BRACE);
$output = [];

foreach ($files as $file) {
    $text   = loadDocument($file);
    $chunks = chunkText($text, 800, 150);

    foreach ($chunks as $i => $chunk) {
        $output[] = [
            'source'      => basename($file),
            'title'       => pathinfo($file, PATHINFO_FILENAME),
            'chunk_index' => $i,
            'content'     => $chunk,
        ];
    }

    echo "Loaded " . basename($file) . ": " . count($chunks) . " chunks\n";
}

file_put_contents(
    __DIR__ . '/chunks.json',
    json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "\nTotal: " . count($output) . " chunks saved to chunks.json\n";
