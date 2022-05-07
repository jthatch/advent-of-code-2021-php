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

        // return points with more than 1 overlap
        return collect($map)
            ->sum(
                fn ($pos): int           => collect($pos)
                    ->filter(fn (int $p) => $p > 1)
                    ->count()
            );
    }

    public function solvePart2(): ?int
    {
        $map = $this->buildMap(input: $this->input);

        // return points with more than 1 overlap
        return collect($map)
            ->sum(
                fn ($pos): int           => collect($pos)
                    ->filter(fn (int $p) => $p > 1)
                    ->count()
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
            ->map(fn (string $line) => sscanf($line, '%d,%d -> %d,%d'))
            ->when(
                $removeDiagonals,
                fn (Collection $collection) => $collection->filter(
                    // horizontals are when x1 = x2 or y1 = y2
                    fn (array $pos) => $pos[0] === $pos[2] || $pos[1] === $pos[3]
                )
            )
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
