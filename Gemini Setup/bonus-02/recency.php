<?php
// recency.php — blend relevance with freshness
function recencyWeight(?string $publishedAt, float $halfLifeHours = 48): float {
    if (!$publishedAt) return 0.5;                 // unknown date -> neutral
    $ageHours = max(0, (time() - strtotime($publishedAt)) / 3600);
    return pow(0.5, $ageHours / $halfLifeHours);   // halves every $halfLifeHours
}

function applyRecency(array $results, float $alpha = 0.7, float $halfLifeHours = 48): array {
    if (empty($results)) return $results;

    $maxRrf = max(array_column($results, 'rrf')) ?: 1;

    foreach ($results as &$r) {
        $relevance = $r['rrf'] / $maxRrf;                               // 0..1
        $freshness = recencyWeight($r['published_at'], $halfLifeHours); // 0..1
        $r['final'] = $alpha * $relevance + (1 - $alpha) * $freshness;
    }
    unset($r);

    usort($results, fn($a, $b) => $b['final'] <=> $a['final']);
    return $results;
}
