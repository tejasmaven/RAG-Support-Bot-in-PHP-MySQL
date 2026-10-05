<?php
// news-context.php — format articles with source, time, and link
function buildNewsContext(array $chunks): array {
    $blocks  = [];
    $sources = [];

    foreach ($chunks as $i => $c) {
        $n    = $i + 1;
        $when = $c['published_at'] ?? 'unknown date';

        $blocks[] = "[{$n}] {$c['source_name']} — published {$when}\n"
                  . "Title: {$c['title']}\n{$c['content']}";

        $sources[$n] = [
            'name' => $c['source_name'],
            'when' => $when,
            'url'  => $c['article_url'],
        ];
    }

    return [implode("\n\n---\n\n", $blocks), $sources];
}
