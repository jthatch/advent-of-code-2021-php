<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day1 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $depthIncreases = 0;

        collect(array_map('intval', $this->input))
            ->sliding(2)
            ->eachSpread(function ($previous, $current) use (&$depthIncreases): void {
                if ($current > $previous) {
                    ++$depthIncreases;
                }
            });

        return $depthIncreases;
    }

    public function solvePart2(): ?int
    {
        $sumIncreases = 0;

        /** @var Collection|null $previous */
        $previous = null;
        collect(array_map('intval', $this->input))
            ->sliding(3)
            ->each(function (Collection $current) use (&$sumIncreases, &$previous): void {
                if ($previous && $current->sum() > $previous->sum()) {
                    ++$sumIncreases;
                }
                $previous = $current;
            });

        return $sumIncreases;
    }
}
