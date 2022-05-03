<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day3 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $gamma   = [];
        $epsilon = [];
        $len     = strlen($this->input[0]);
        $bits    = [];

        // from input, build array of $len size containing the position offsets, e.g. Nth character from each row
        foreach ($this->input as $row) {
            for ($n = 0; $n < $len; ++$n) {
                $bits[$n] ??= [];
                $bits[$n][] = (int) $row[$n];
            }
        }

        // find most common (gamma) and least common (epsilon)
        collect($bits)
            ->each(function ($item) use (&$gamma, &$epsilon): void {
                [$gamma[], $epsilon[]] = collect($item)
                    ->countBy()
                    ->sortDesc()
                    ->keys()
                    ->toArray();
            });

        // convert binary to decimal
        $gammaRate   = bindec(implode('', $gamma));
        $epsilonRate = bindec(implode('', $epsilon));

        return (int) ($gammaRate * $epsilonRate);
    }

    public function solvePart2(): ?int
    {
        $oxygen = $this->ratingTraverse($this->input);
        $co2    = $this->ratingTraverse($this->input, false);

        // convert binary to decimal
        $oxygenRate = bindec($oxygen);
        $co2Rate    = bindec($co2);

        return (int) ($co2Rate * $oxygenRate);
    }

    /**
     * @param array<int, string> $input
     * @param bool               $oxygenRatingCriteria
     * @param int                $position
     *
     * @return string
     */
    protected function ratingTraverse(array $input, bool $oxygenRatingCriteria = true, int $position = 0): string
    {
        if (count($input) < 2) {
            return array_pop($input) ?? '';
        }

        // build a set of every bit at position
        $bitFrequency = collect($input)
            ->map(fn ($item) => (int) $item[$position])
            ->countBy()
            ->sortDesc()
            ->toArray();

        // oxygen rating is the most common value in the bit position defaulting to 1
        if ($oxygenRatingCriteria) {
            $ratingCriteriaBit = $bitFrequency[1] !== $bitFrequency[0]
                ? array_key_first($bitFrequency)
                : 1;
        } else { // co2 rating is the least common value in the bit position defaulting to 0
            $ratingCriteriaBit = $bitFrequency[1] !== $bitFrequency[0]
                ? array_key_last($bitFrequency)
                : 0;
        }

        // keep only rows with the matching bit in the position
        $input = collect($input)
            ->filter(function ($row) use ($position, $ratingCriteriaBit) {
                return (int) $row[$position] === $ratingCriteriaBit;
            })
            ->toArray();

        // call ourselves recursively with the new input
        return $this->ratingTraverse($input, $oxygenRatingCriteria, ++$position);
    }
}
