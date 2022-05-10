<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day7 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));
        // create histogram based on distance between crab and target position
        $deltaReduce = fn (int $from, int $to): int => abs($from - $to);

        return $this->fuelHistogram($input, $deltaReduce);
    }

    public function solvePart2(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));
        // this histogram is based on the sum of all numbers between crab and target position
        $sumDeltaReduce = function (int $from, int $to): int {
            $delta = abs($from - $to);

            return (int) ($delta * ($delta + 1) / 2);
        };

        return $this->fuelHistogram($input, $sumDeltaReduce);
    }

    /**
     * @param array<int, int> $input
     * @param callable        $deltaReduce
     *
     * @return int
     */
    protected function fuelHistogram(array $input, callable $deltaReduce): int
    {
        sort($input);
        $crabs     = collect($input);
        $positions = collect()->range($input[0], $input[count($input) - 1]);

        return (int) $positions
            // this histogram is based on the sum of all numbers between crab and target position
            ->map(fn (int $from) => $crabs->reduce(
            // cache the range sum to save time
                fn (int $carry, int $to) => $deltaReduce($from, $to) + $carry,
                0
            ))
            ->sort()
            ->first();
    }
}
