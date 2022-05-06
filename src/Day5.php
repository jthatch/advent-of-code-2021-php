<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

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
     */
    protected function buildMap(array $input, bool $removeDiagonals = false): array
    {
        $map        = [];
        $collection = collect($input)
            ->map(function ($item) {
                preg_match_all('/(\d+)/', $item, $matches);

                return [
                    (int) $matches[1][0], // x1
                    (int) $matches[1][1], // y1
                    (int) $matches[1][2], // x2
                    (int) $matches[1][3], // y2
                ];
            });

        if ($removeDiagonals) {
            // horizontals are when x1 = x2 or y1 = y2
            $collection = $collection->filter(fn ($pos) => $pos[0] === $pos[2] || $pos[1] === $pos[3]);
        }

        $collection->eachSpread(function (int $x1, int $y1, int $x2, int $y2) use (&$map): void {
            $xRange = range($x1, $x2);
            $yRange = range($y1, $y2);
            $lastY  = null;
            $lastX  = null;
            while (!empty($xRange) || !empty($yRange)) {
                $x = (int) (array_pop($xRange) ?? $lastX);
                $y = (int) (array_pop($yRange) ?? $lastY);
                $map[$y][$x] ??= 0;
                ++$map[$y][$x];
                $lastX = $x;
                $lastY = $y;
            }
        });

        return $map;
    }
}
