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

        return $this->fishTraverseUsingArray(collect($input), 80);
    }

    /**
     * This approach works by keeping track of the number of fish of a given age.
     *
     * @return int|null
     */
    public function solvePart2(): ?int
    {
        $input = array_map('intval', explode(',', $this->input[0]));

        // build a collection of $fish [age => fishCount] key/values, 0-8
        /** @var Collection<array<int, int>> $fish */
        $fish = collect()->pad(9, 0)
            ->replace(array_count_values($input));

        // as each day progresses, the fishCount of age (n) are set to (n)+1
        // to handle the creation of new fish, the aged 0 fish are added to the age 8 fish
        $days = 256;
        while ($days-- > 0) {
            $fish->transform(
                // decrease the age of each fish by negatively rotating the fishCount down one, e.g. age 1 fish move to age 0 etc…
                // for age 6 fish we spawn (n) new fish (n) = age 0 fish
                fn ($c, int $age): int => $fish->get(++$age > 8 ? 0 : $age) + (7 === $age
                        ? $fish->get(0) // every fish age 0 will spawn a new fish age 8
                        : 0)
            );
        }

        return (int) $fish->sum();
    }

    /**
     * This method is useful for seeing what happens but is not memory efficient.
     *
     * @param Collection $fish
     * @param int        $days
     *
     * @return int
     */
    public function fishTraverseUsingArray(Collection $fish, int $days): int
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

        return $this->fishTraverseUsingArray($fish, --$days);
    }
}
