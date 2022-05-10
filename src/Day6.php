<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day6 extends DayBehaviour
{
    protected array $days = [];

    public function solvePart1(): ?int
    {
        $input = array_map('intval', explode(',', $this->example()[0]));
        // $input = array_map('intval', explode(',', $this->input[0]));

        return $this->fishTraverse(collect($input), 80);
        // return $this->fishTraverse(collect([3,4]), 18);
    }

    public function solvePart2(): ?int
    {
        $input = array_map('intval', explode(',', $this->example()[0]));
        // $input = array_map('intval', explode(',', $this->input[0]));

        $count = count($input);
        // $count = 0;
        $fishes = array_count_values($input);
        foreach ($fishes as $fishAge => $total) {
            // foreach($input as $fishAge) {
            // $count++;
            $totalForFish = $this->fishTraverseNew($fishAge, 256, 0, 0);
            $totalForFish *= $total;
            $count += $totalForFish;
        }

        /*foreach($this->days as $day => $fish) {
            printf("days: %d count: %d fish: %s\n", $day, count($fish), implode(",", $fish));
        }*/

        return $count;

        /* return $count;
         //$count = count($input);
         $count = 0;
         foreach ($input as $fish) {
             $fishCount = $this->howManyNewFish($fish, 18, 0);
             $count += $fishCount;
         }

         return (int) $count;*/
        // return $this->fishTraverseNew($input, 256);
    }

    public function fishTraverseNew(int $fishAge, int $days, int $children = 0, int $newLife = 0): int
    {
        // $this->days[$days][] = $fishAge;
        // printf("days: %d fish: %d children: %d newlife: %d\n", $days, $fishAge, $children, $newLife);
        if (0 === $days) {
            return $newLife + $children;
        }

        --$days;

        if (0 === $fishAge) {
            $fishAge = 6;
            ++$newLife;
            $children += $this->fishTraverseNew(8, $days, 0, 0);
        } else {
            --$fishAge;
        }

        return $this->fishTraverseNew($fishAge, $days, $children, $newLife);
    }

    public function fishTraverse(Collection $fish, int $days): int
    {
        printf("day: %d count: %d fish: %s\n", $days, $fish->count(), $fish->join(','));

        if (0 === $days) {
            return $fish->count();
        }

        $append = [];
        $fish
            ->transform(function ($age) use (&$append) {
                if (0 === $age) {
                    $append[] = 8;

                    return 6;
                }

                return --$age;
            })
            ->push(...$append);

        return $this->fishTraverse($fish, --$days);
    }

    protected function example(): array
    {
        return ['3,4,3,1,2'];
        // return ['3,4'];
    }
}
