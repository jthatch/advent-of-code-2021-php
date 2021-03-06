<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use App\Day11\Octopus;
use Illuminate\Support\Collection;

class Day11 extends DayBehaviour
{
    // [[y,x],..] each of the 8 possible adjacent octopuses, starting top left going clockwise
    private array $adjacent = [[-1, -1], [-1, 0], [-1, 1], [0, 1], [1, 1], [1, 0], [1, -1], [0, -1]];

    public function solvePart1(): ?int
    {
        $octopuses = $this->mapToOctopuses($this->input);

        $totalFlashes = 0;
        foreach (range(1, 100) as $i) {
            $totalFlashes += $octopuses->reduce(fn (int $c, Octopus $octopus) => $c + $octopus->energise(), 0);
            $octopuses->each(fn (Octopus $octopus) => $octopus->cooldown());
        }

        return $totalFlashes;
    }

    public function solvePart2(): ?int
    {
        $octopuses = $this->mapToOctopuses($this->input);

        $allFlashCount = $octopuses->count();
        $stepCount     = 0;
        do {
            ++$stepCount;
            $octopuses->each(fn (Octopus $octopus) => $octopus->cooldown());
        } while ($allFlashCount !== $octopuses->reduce(fn (int $c, Octopus $octopus) => $c + $octopus->energise(), 0));

        return $stepCount;
    }

    protected function mapToOctopuses(array $input): Collection
    {
        // map to [y][x] grid
        $octopuses = collect($input)
            ->map(fn (string $row) => str_split($row))
            ->flatMap(fn (array $row, int $y) => [
                $y => collect($row)->flatMap(fn (int $energy, int $x) => [
                    $x => new Octopus($energy),
                ]),
            ]);

        // reorient to a flat list and assign octopus neighbours
        return $octopuses
            ->each(fn (Collection $row, int $y) => $row->each(fn (Octopus $octopus, int $x) => $octopus->neighbours = collect($this->adjacent)
                ->filter(fn (array $pos) => isset($octopuses[$y + $pos[0]][$x + $pos[1]]))
                ->map(fn (array $pos)    => $octopuses[$y + $pos[0]][$x + $pos[1]])))
            ->flatten();
    }

    /**
     * @deprecated
     */
    public function solvePart1Old(): ?int
    {
        $octopuses = array_map(static fn (string $s): array => array_map('intval', str_split($s)), $this->input);

        return $this->step($octopuses, 100);
    }

    /**
     * @deprecated
     */
    public function solvePart2Old(): ?int
    {
        $octopuses = array_map(static fn (string $s): array => array_map('intval', str_split($s)), $this->input);

        $detectSimultaneousFlash = fn (array $octopuses) => collect($octopuses)->reject(fn ($row) => collect($row)->reject(fn ($o) => 0 === $o)->isEmpty())->isEmpty();

        return $this->step($octopuses, PHP_INT_MAX, $detectSimultaneousFlash);
    }

    /**
     * @deprecated
     */
    protected function step(array $octopuses, int $steps, ?callable $breakAfterStep = null): int
    {
        $totalFlashes = 0;
        $stepCount    = 0;
        while ($stepCount++ < $steps) {
            $flashing = [];
            for ($y = 0, $yMax = count($octopuses); $y < $yMax; ++$y) {
                for ($x = 0, $xMax = count($octopuses[0]); $x < $xMax; ++$x) {
                    if (++$octopuses[$y][$x] > 9) {
                        $octopuses[$y][$x] = 0;
                        $flashing[]        = [$y, $x];
                    }
                }
            }
            // loop over our flashers and increment all adjacent octopuses, adding any that reach >9 to our set.
            while (!empty($flashing)) {
                ++$totalFlashes;
                [$y, $x] = array_pop($flashing);
                collect($this->adjacent)->filter(function (array $pos) use ($y, $x, &$octopuses, &$flashing) {
                    $y += $pos[0];
                    $x += $pos[1];
                    $neighbour = $octopuses[$y][$x] ?? null;
                    if (!is_null($neighbour) && 0 !== $neighbour && ++$octopuses[$y][$x] > 9) {
                        $octopuses[$y][$x] = 0;
                        $flashing[]        = [$y, $x];
                    }
                });
            }

            // pass octopuses to our callable and break early if callable returns true
            if (is_callable($breakAfterStep) && $breakAfterStep($octopuses)) {
                return $stepCount;
            }

            // helpful debugging output when developing this.
            // printf("After step %s:\n", $steps);
            // collect($octopuses)->each(fn ($row) => printf("%s\n", implode('', $row)));
            // echo "\n";
        }

        return $totalFlashes;
    }

    public function example(): array
    {
        return explode(
            "\n",
            <<<eof
5483143223
2745854711
5264556173
6141336146
6357385478
4167524645
2176841721
6882881134
4846848554
5283751526
eof
        );
    }
}
