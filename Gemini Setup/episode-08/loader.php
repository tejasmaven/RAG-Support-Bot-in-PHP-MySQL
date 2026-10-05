<?php
// loader.php — load a document and return clean plain text
//
// PDF support is optional: run `composer require smalot/pdfparser` to enable it.
// Markdown/text files work with no dependencies.

$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
}

function loadDocument(string $path): string {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $raw = match ($ext) {
        'md', 'txt' => file_get_contents($path),
        'pdf'       => (function (string $p): string {
            if (!class_exists('Smalot\\PdfParser\\Parser')) {
                throw new RuntimeException('PDF support needs: composer require smalot/pdfparser');
            }
            return (new \Smalot\PdfParser\Parser())->parseFile($p)->getText();
        })($path),
        default     => throw new RuntimeException("Unsupported file type: .$ext"),
    };

    return cleanText($raw);
}

function cleanText(string $text): string {
    // Normalize line endings across OSes
    $text = str_replace(["\r\n", "\r"], "\n", $text);

    // Strip common Markdown markers so the model sees plain content
    $text = preg_replace('/^#{1,6}\s*/m', '', $text);   // heading hashes
    $text = preg_replace('/[*_`>]/', '', $text);         // emphasis, code, quote marks

    // Tidy whitespace
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);

    return trim($text);
}
