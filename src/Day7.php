<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day7 extends DayBehaviour
{
    protected array $cache = [];

    public function solvePart1(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));
        sort($input);
        $crabs     = collect($input);
        $positions = collect($input)->unique()->sort()->values();

        return (int) $positions
            // create histogram based on distance between crab and target position
            ->map(fn ($p, $z) => $crabs->reduce(fn ($a, $c) => abs($c - $p) + $a, 0))
            ->sort()
            ->first()
        ;
    }

    public function solvePart2(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));
        sort($input);
        $crabs     = collect($input);
        $positions = collect()->range($input[0], $input[count($input) - 1]);

        return (int) $positions
            // this histogram is based on the sum of all numbers between crab and target position
            ->map(fn ($from) => $crabs->reduce(
                // cache the range sum to save time
                fn ($a, $to) => ($this->cache[abs($from - $to)] ??= array_sum(range(1, abs($from - $to)))) + $a,
                0
            ))
            ->sort()
            ->first();
    }
}
