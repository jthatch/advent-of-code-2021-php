<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day7 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $input   = array_map('intval', explode(',', $this->input[0]));
        $crabs   = collect($input)->sort();
        $average = $crabs->median(); // based on part 1's example of 2, the answer will be the most common (median) position
        // create histogram based on distance between crab and target position
        $deltaReduce = fn (int $from, int $to): int => abs($from - $to);

        return $this->fuelHistogram($crabs, $deltaReduce, $average);
    }

    public function solvePart2(): ?int
    {
        $input   = array_map('intval', explode(',', $this->input[0]));
        $crabs   = collect($input)->sort();
        $average = $crabs->average(); // for part 2 it's costly for single crabs to move longer distances, so go for average
        // this histogram is based on the sum of all numbers between crab and target position
        $sumDeltaReduce = function (int $from, int $to): int {
            $delta = abs($from - $to);

            return (int) ($delta * ($delta + 1) / 2);
        };

        return $this->fuelHistogram($crabs, $sumDeltaReduce, $average);
    }

    /**
     * @param Collection<int, int> $crabs
     * @param callable             $deltaReduce
     * @param float|null           $averagePosition
     *
     * @return int
     */
    protected function fuelHistogram(Collection $crabs, callable $deltaReduce, ?float $averagePosition = null): int
    {
        // if we have an estimated guess use that as it'll save a bunch of cycles, falling back to min/max of crabs
        $positions = $averagePosition
            ? collect([(int) floor($averagePosition), ceil($averagePosition)])
            : collect()->range($crabs->min(), $crabs->max());

        return (int) $positions
            ->map(fn (int $from) => $crabs->reduce(fn (int $carry, int $to) => $deltaReduce($from, $to) + $carry, 0))
            ->sort()
            ->first();
    }
}
