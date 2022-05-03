<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day2 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $input = array_map(fn (string $line) => explode(' ', $line), $this->input);
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

        return $pos['hoz'] * $pos['dep'];
    }

    public function solvePart2(): ?int
    {
        $input = array_map(fn (string $line) => explode(' ', $line), $this->input);
        $pos = [
            'hoz' => 0,
            'dep' => 0,
            'aim' => 0,
        ];

        foreach ($input as [$instruction, $value]) {
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
        }

        return $pos['hoz'] * $pos['dep'];
    }
}
