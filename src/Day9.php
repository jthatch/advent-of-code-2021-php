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
        $lowPoints = [];

        for ($y = 0, $yMax = count($heightmap); $y < $yMax; ++$y) {
            for ($x = 0, $xMax = count($heightmap[0]); $x < $xMax; ++$x) {
                $location = $heightmap[$y][$x];

                // loop over our adjacent positions, if none are bigger then we've found a low point
                if (empty(array_filter(
                    $this->adjacent,
                    fn (array $pos) => ($adjacent = $heightmap[$y + $pos[0]][$x + $pos[1]] ?? null) !== null && $location >= $adjacent
                ))) {
                    $lowPoints[] = $location;
                }
            }
        }

        return collect($lowPoints)->reduce(fn (int $c, int $n) => (1 + $n) + $c, 0);
    }

    public function solvePart2(): ?int
    {
        // TODO: Implement solvePart2() method.
        return null;
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
