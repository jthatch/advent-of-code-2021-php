<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

/**
 * Very similar to Day11 on AOC 2020.
 *
 * @see https://github.com/jthatch/advent-of-code-php-2020/blob/master/src/Day11.php
 */
class Day9 extends DayBehaviour
{
    // [[y,x],..] each of the 4 possible adjacent locations, starting top going clockwise
    private array $adjacent = [[-1, 0], [0, 1], [1, 0], [0, -1]];

    /**
     * Working with coordinates isn't really collect()'s strong suit, so did this the old-fashioned way.
     *
     * @return int|null
     */
    public function solvePart1(): ?int
    {
        $heightmap = array_map(static fn (string $s): array => array_map('intval', str_split($s)), $this->input);

        return collect($this->getLowPoints($heightmap))
            ->sum(fn ($n) => ++$n); // return sum of all low points (1 plus height)
    }

    public function solvePart2(): ?int
    {
        $heightmap = array_map(static fn (string $s): array => array_map('intval', str_split($s)), $this->input);

        return collect($this->getLowPoints($heightmap))
            ->map(function (int $value, string $coordinates) use ($heightmap) {
                $basins[$coordinates] = $value;
                // loop over each basin and travel in each direction
                $key = array_key_first($basins);
                while ($key) {
                    [$y, $x] = array_map('intval', explode('-', $key));
                    collect($this->adjacent)
                        ->each(function (array $pos) use ($y, $x, $value, $heightmap, &$basins): void {
                            while (true) {
                                // keep travelling in the adjacent direction
                                $y += $pos[0];
                                $x += $pos[1];
                                $key = sprintf('%s-%s', $y, $x);
                                // determine if we've seen this basin already
                                if (isset($basins[$key])) {
                                    break;
                                }
                                // determine if we hit a wall
                                if ($y < 0 || $x < 0 || $y > (count($heightmap) - 1) || $x > (count($heightmap[0]) - 1)) {
                                    break; // we hit a wall
                                }
                                $location = $heightmap[$y][$x];
                                if ($location <= $value || 9 === $location) {
                                    break; // location is a dead end
                                }

                                $basins[$y.'-'.$x] = $location;
                            }
                        });
                    $key = next($basins)
                        ? key($basins)
                        : null;
                }

                return $basins;
            })
            ->sortByDesc(fn ($b) => count($b))
            ->take(3)
            ->reduce(fn (int $c, array $b) => $c * count($b), 1);
    }

    /**
     * @param array $heightmap
     *
     * @return array<string, int> $input key in format `y-x`
     */
    protected function getLowPoints(array $heightmap): array
    {
        $lowPoints = [];
        for ($y = 0, $yMax = count($heightmap); $y < $yMax; ++$y) {
            for ($x = 0, $xMax = count($heightmap[0]); $x < $xMax; ++$x) {
                $location = $heightmap[$y][$x];

                // loop over our adjacent positions, if none are bigger then we've found a low point
                if (empty(array_filter(
                    $this->adjacent,
                    fn (array $pos) => ($adjacent = $heightmap[$y + $pos[0]][$x + $pos[1]] ?? null) !== null && $location >= $adjacent
                ))) {
                    $key             = sprintf('%s-%s', $y, $x);
                    $lowPoints[$key] = $location;
                }
            }
        }

        return $lowPoints;
    }

    public function example(): array
    {
        return explode("\n", <<<eof
2199943210
3987894921
9856789892
8767896789
9899965678
eof);
    }
}
