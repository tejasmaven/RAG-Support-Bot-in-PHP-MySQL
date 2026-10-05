<?php
// vector.php — scale a vector to length 1
function normalize(array $vec): array {
    $sum = 0.0;
    foreach ($vec as $v) {
        $sum += $v * $v;
    }
    $length = sqrt($sum);
    if ($length == 0.0) {
        return $vec; // avoid dividing by zero
    }
    return array_map(fn($v) => $v / $length, $vec);
}
