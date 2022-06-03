<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day2 extends DayBehaviour
{
    /**
     *  Refactored this using collect, which seems a few orders of magnitude slower :(
     *  Mem[616kb] Peak[ 1013kb] Time[0.06339s]   <-- Using collect()
     *  Mem[422kb] Peak[  925kb] Time[0.00072s]   <-- Using commented out array functionality.
     */
    public function solvePart1(): ?int
    {
        $pos = collect($this->input)
            ->reduce(function (array $pos, string $cmd) {
                [$instruction, $value] = explode(' ', $cmd);
                match ($instruction) {
                    'forward' => $pos['hoz'] += (int) $value,
                    'down'    => $pos['dep'] += (int) $value,
                    'up'      => $pos['dep'] -= (int) $value,
                };

                return $pos;
            }, ['hoz' => 0, 'dep' => 0]);

        return $pos['hoz'] * $pos['dep'];

        /*$input = array_map(fn (string $line) => explode(' ', $line), $this->input);
        $pos = [
            'hoz' => 0,
            'dep' => 0,
        ];

        foreach ($input as [$instruction, $value]) {
            match ($instruction) {
                'forward' => $pos['hoz'] += (int) $value,
                'down'    => $pos['dep'] += (int) $value,
                'up'      => $pos['dep'] -= (int) $value,
            };
        }

        return $pos['hoz'] * $pos['dep'];*/
    }

    public function solvePart2(): ?int
    {
        $pos = collect($this->input)
            ->reduce(function (array $pos, string $cmd) {
                [$instruction, $value] = explode(' ', $cmd);
                switch ($instruction) {
                    case 'forward':
                        $pos['hoz'] += (int) $value;
                        $pos['dep'] += (int) $value * $pos['aim'];
                        break;
                    case 'down':
                        $pos['aim'] += (int) $value;
                        break;
                    case 'up':
                        $pos['aim'] -= (int) $value;
                        break;
                }

                return $pos;
            }, ['hoz' => 0, 'dep' => 0, 'aim' => 0]);

        return $pos['hoz'] * $pos['dep'];
    }
}
