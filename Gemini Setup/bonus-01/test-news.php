<?php
// test-news.php — quick look at what retrieval returns
require __DIR__ . '/retrieve-news.php';

foreach (retrieveNews($argv[1] ?? 'Surat traffic') as $i => $r) {
    printf("#%d  [%s | %s]\n%s\n\n",
        $i + 1,
        $r['source_name'],
        $r['published_at'],
        mb_substr($r['title'] . ' - ' . $r['content'], 0, 140) . '...'
    );
}
