<?php

declare(strict_types=1);

namespace App\Day11;

use Illuminate\Support\Collection;

class Octopus
{
    protected bool $hasFlashed = false;

    public function __construct(public int $energy, public Collection $neighbours = new Collection())
    {
    }

    public function energise(): int
    {
        if (++$this->energy > 9 && !$this->hasFlashed) {
            return 1 + $this->flash();
        }

        return 0;
    }

    public function cooldown(): void
    {
        if ($this->hasFlashed) {
            $this->energy     = 0;
            $this->hasFlashed = false;
        }
    }

    protected function flash(): int
    {
        $this->hasFlashed = true;

        return $this->neighbours->reduce(fn (int $c, Octopus $octopus): int => $c + $octopus->energise(), 0);
    }
}
