<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day5 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $map = $this->buildMap(input: $this->input, removeDiagonals: true);

        return collect($map)
            ->sum(
                // return points with more than 1 overlap
                fn ($pos): int => collect($pos)->filter(fn (int $p) => $p > 1)->count()
            );
    }

    public function solvePart2(): ?int
    {
        $map = $this->buildMap(input: $this->input);

        return collect($map)
            ->sum(
                fn ($pos): int => collect($pos)->filter(fn (int $p) => $p > 1)->count()
            );
    }

    /**
     * @param array<string> $input
     * @param bool          $removeDiagonals
     *
     * @return array<int, array<int>>
     * @noinspection PrintfScanfArgumentsInspection
     */
    protected function buildMap(array $input, bool $removeDiagonals = false): array
    {
        $map = [];

        collect($input)
            // extract [x1,y1,x2,y2] integers from line
            ->map(fn (string $line) => sscanf($line, '%d,%d -> %d,%d'))
            // for part 1, we remove any lines that aren't horizontal or vertical
            // by checking that x1 = x2 or y1 = y2
            ->when(
                $removeDiagonals,
                fn (Collection $collection) => $collection->filter(
                    fn (array $pos): bool   => $pos[0] === $pos[2] || $pos[1] === $pos[3]
                )
            )
            // loop over each line and populate our map
            // the point increments each time a line overlaps another
            ->eachSpread(function (int $x1, int $y1, int $x2, int $y2) use (&$map): void {
                $xRange = range($x1, $x2);
                $yRange = range($y1, $y2);
                // pad arrays to the same size, repeating last number
                $xRange = array_pad($xRange, count($yRange), $xRange[count($xRange) - 1]);
                $yRange = array_pad($yRange, count($xRange), $yRange[count($yRange) - 1]);
                while (!empty($xRange) || !empty($yRange)) {
                    $x = (int) array_pop($xRange);
                    $y = (int) array_pop($yRange);
                    $map[$y][$x] ??= 0;
                    ++$map[$y][$x];
                }
            });

        return $map;
    }
}
