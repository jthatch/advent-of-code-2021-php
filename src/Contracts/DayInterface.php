<?php

declare(strict_types=1);

namespace App\Contracts;

interface DayInterface
{
    public function solvePart1(): int|string|null;

    public function solvePart2(): int|string|null;

    public function day(): string;
}
