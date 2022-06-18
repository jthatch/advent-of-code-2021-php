<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day13 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        [$grid, $folds] = $this->generateGridAndFolds($this->input);
        // only process first fold
        $folds = [array_shift($folds)];

        return $this
            ->foldPaper($grid, $folds)
            ->reduce(fn (int $carry, array $grid) => $carry + count($grid), 0);
    }

    public function solvePart2(): ?int
    {
        [$grid, $folds] = $this->generateGridAndFolds($this->input);
        $grid           = $this->foldPaper($grid, $folds);

        // find the max Y and X coordinates
        $maxY = $grid->keys()->sort()->last();
        $maxX = $grid->reduce(fn (int $max, array $xGrid) => ($k = collect($xGrid)->keys()->sort()->last()) > $max ? $k : $max, 0);

        // print out the "eight capital letters"
        for ($y = 0; $y <= $maxY; ++$y) {
            echo '      ';
            for ($x = 0; $x <= $maxX; ++$x) {
                printf('%1s', $grid[$y][$x] ?? '');
            }
            echo "\n";
        }

        return $grid
            ->reduce(fn (int $carry, array $grid) => $carry + count($grid), 0);
    }

    protected function foldPaper(Collection $grid, array $folds): Collection
    {
        foreach ($folds as $fold) {
            [$axis, $point] = $fold;
            // process the folds. y =  fold horizontally, bottom half up, x = fold vertically, right half to left
            $grid = match ($axis) {
                'y' => $grid
                    ->mapToGroups(fn (array $xGrid, int $y) => [$y > $point ? $point - ($y - $point) : $y => $xGrid])
                    ->map(fn ($xGrid, int $y) => $xGrid->mapWithKeys(fn (array $xStack) => collect($xStack)->all())->all()),
                'x' => $grid
                    ->map(fn (array $xGrid, int $y) => collect($xGrid)
                        ->mapWithKeys(fn (string $dot, int $x) => [$x > $point ? $point - ($x - $point) : $x => $dot])->toArray()),
            };
        }

        return $grid;
    }

    protected function generateGridAndFolds(array $input): array
    {
        $grid  = [];
        $folds = [];
        collect($input)->each(function (string $line) use (&$grid, &$folds) {
            if (str_contains($line, ',')) { // first part are "x,y" coordinates for dot placement on grid
                [$x, $y] = sscanf($line, '%d,%d');
                $grid[$y][$x] ??= '#';
            } elseif (str_contains($line, 'fold along')) { // finally build up a list of "folds" we must make
                [,,$axis, $foldPoint] = sscanf($line, '%s %s %[^=]=%d');
                $folds[]              = [$axis, $foldPoint];
            }
        });

        return [
            collect($grid),
            $folds,
        ];
    }
}
