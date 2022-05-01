<?php

declare(strict_types=1);

namespace App\Contracts;

abstract class DayBehaviour implements DayInterface
{
    /**
     * @param array<int, int|string> $input
     */
    public function __construct(protected array $input)
    {
    }

    public function day(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
