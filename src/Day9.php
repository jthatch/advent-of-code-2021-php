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

        // store the max bounds of our grid to avoid looking up each time in a double-nested while loop ;)
        $yMax = count($heightmap);
        $xMax = count($heightmap[0]);

        return collect($this->getLowPoints($heightmap))
            ->map(function (int $value, string $coordinates) use ($heightmap, $yMax, $xMax) {
                // starting from the lowest point, traverse in each direction looking for locations > current & <9
                // appending those to our basin. Each basin location will be processed until the flow ends
                $basins[$coordinates] = $value;
                // loop over each basin, the while loop allows us to continuously process new basin locations until we're done
                $key = array_key_first($basins);
                while ($key) {
                    [$y, $x] = array_map('intval', explode('-', $key));
                    collect($this->adjacent)
                        ->each(function (array $pos) use ($y, $x, $value, $heightmap, &$basins, $yMax, $xMax): void {
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
                                if ($y < 0 || $x < 0 || $y > ($yMax - 1) || $x > ($xMax - 1)) {
                                    break; // we hit a wall
                                }
                                $location = $heightmap[$y][$x];
                                if ($location <= $value || 9 === $location) {
                                    break; // location is a dead end
                                }
                                // append the location to our basin
                                $basins[$y.'-'.$x] = $location;
                            }
                        });
                    // keep going until we've processed all basin locations
                    $key = next($basins)
                        ? key($basins)
                        : null;
                }

                return $basins;
            })
            // find the three largest basins and multiply their sizes together
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
