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
            ->reduce(fn (int $carry, array $grid) => $carry + (array_count_values($grid)['#'] ?? 0), 0);
    }

    public function solvePart2(): ?int
    {
        [$grid, $folds] = $this->generateGridAndFolds($this->input);
        $grid           = $this->foldPaper($grid, $folds);

        // print out the "eight capital letters"
        $grid->each(fn ($row) => printf("%s\n", collect($row)->join('')));

        return $grid
            ->reduce(fn (int $carry, $g) => $carry + (array_count_values($g)['#'] ?? 0), 0);
    }

    protected function foldPaper(Collection $grid, array $folds): Collection
    {
        foreach ($folds as $fold) {
            [$axis, $foldPoint] = $fold;
            if ('y' === $axis) { // fold horizontally, bottom half up
                $foldedGrid = $grid->splice($foldPoint);
                $foldedGrid->shift(); // remove row line is on.
                $grid = $grid->replaceRecursive(
                    collect()->range(--$foldPoint, $foldPoint - ($foldedGrid->count() - 1))
                        ->combine($foldedGrid) // reindex our fold based on positions we're folding onto
                        ->transform(fn ($g) => collect($g)->filter(fn (string $v) => '#' === $v))->toArray()
                );
            } else { // fold vertically, right half to left
                $grid->transform(function (array $row) use ($foldPoint) {
                    $gridRow    = collect($row);
                    $foldedGrid = $gridRow->splice($foldPoint);
                    $foldedGrid->shift(); // remove row line is on.

                    return $gridRow->replace(
                        collect()->range(--$foldPoint, $foldPoint - ($foldedGrid->count() - 1))
                            ->combine($foldedGrid)
                            ->filter(fn (string $v) => '#' === $v)
                    )->all();
                });
            }
        }

        return $grid;
    }

    protected function generateGridAndFolds(array $input): array
    {
        $grid  = [];
        $folds = [];
        $max   = collect(['x' => 0, 'y' => 0]);
        collect($input)->each(function (string $line) use (&$grid, &$folds, $max) {
            if (str_contains($line, ',')) { // first part are "x,y" coordinates for dot placement on grid
                [$x, $y] = array_map('intval', explode(',', $line));
                $grid[$y][$x] ??= '#';
                if ($x > $max['x']) {
                    $max['x'] = $x;
                }
                if ($y > $max['y']) {
                    $max['y'] = $y;
                }
            } elseif ('' === $line) { // once we encounter a blank line we resize, reorder and populate grid, filling in remaining positions with '.'
                $max = $max->toArray();
                ++$max['y'];
                ++$max['x'];
                $grid = collect()->pad($max['y'], array_fill(0, $max['x'], '.'))->replaceRecursive($grid)->all();
            } elseif (str_contains($line, 'fold along')) { // finally build up a list of "folds" we must make
                $line               = str_replace('fold along ', '', $line);
                [$axis, $foldPoint] = explode('=', $line);
                $folds[]            = [$axis, (int) $foldPoint];
            }
        });

        return [
            collect($grid),
            $folds,
        ];
    }
}
