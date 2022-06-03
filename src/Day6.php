<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day6 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));

        return $this->fishRotate($input, 80);
    }

    public function solvePart2(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));

        return $this->fishRotate($input, 256);
    }

    /**
     * This approach works by keeping track of the number of fish of a given age.
     *
     * @param array<int, int> $input
     */
    protected function fishRotate(array $input, int $days): int
    {
        // build a collection of $fish [age => fishCount] key/values, 0-8
        /** @var Collection<array<int, int>> $fish */
        $fish = collect()->pad(9, 0)->replace(array_count_values($input));

        // as each day progresses the fishes breeding cycle gets one day closer. We keep track of this using `age`, where
        // age 2 fishCount becomes age 1 fishCount, age 1 becomes age 0, and the age 0 fish are added to the age 8 fish
        while ($days-- > 0) {
            $fish->transform(
            // decrease the age of each fish by negatively rotating the fishCount down one, e.g. age 1 fish move to age 0 etc…
            // for age 6 fish we spawn new fish from age 0
                fn ($fC, int $age): int => $fish->get(++$age > 8 ? 0 : $age) + (7 === $age
                        ? $fish->get(0) // every fish age 0 will spawn a new fish age 8
                        : 0)
            );
        }

        return (int) $fish->sum();
    }

    /**
     * This method adds new fish to an array which is useful for seeing what happens but is not memory efficient.
     */
    protected function fishTraverseWithFish(Collection $fish, int $days): int
    {
        // printf("day: %d count: %d fish: %s\n", $days, $fish->count(), $fish->join(','));
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

        return $this->fishTraverseWithFish($fish, --$days);
    }
}
