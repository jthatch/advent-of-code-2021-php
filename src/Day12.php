<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use App\Day12\Cave;
use Illuminate\Support\Collection;

class Day12 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $caves = $this->mapToCaves($this->input);

        return $caves
            ->get('start')
            ->traverse();
    }

    public function solvePart2(): ?int
    {
        $caves = $this->mapToCaves($this->input);

        return $caves
            ->get('start')
            ->traverse(visitSmallCaveTwice: true);
    }

    /**
     * @return Collection<string, Cave>
     */
    protected function mapToCaves(array $input): Collection
    {
        $caves = collect();
        collect($input)
            ->map(fn (string $line) => explode('-', $line))
            ->eachSpread(function ($cave1, $cave2) use (&$caves) {
                $caves[$cave1] ??= new Cave($cave1);
                $caves[$cave2] ??= new Cave($cave2);

                $caves[$cave1]->adjacent[] = $caves[$cave2];
                $caves[$cave2]->adjacent[] = $caves[$cave1];
            });

        return $caves;
    }
}
