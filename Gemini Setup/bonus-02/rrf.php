<?php
// rrf.php — Reciprocal Rank Fusion (the same function from Episode 7)
function reciprocalRankFusion(array $lists, int $k = 60): array {
    $scores = [];
    $items  = [];

    foreach ($lists as $list) {
        foreach (array_values($list) as $rank => $row) {
            $id          = $row['id'];
            $scores[$id] = ($scores[$id] ?? 0) + 1 / ($k + $rank + 1);
            $items[$id]  = $row;
        }
    }

    arsort($scores);

    $merged = [];
    foreach ($scores as $id => $score) {
        $row        = $items[$id];
        $row['rrf'] = $score;
        $merged[]   = $row;
    }
    return $merged;
}
