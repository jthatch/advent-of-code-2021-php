<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Support\Collection;

class Input
{
    public readonly Collection $collection;

    /**
     * @param array<int, int|string|array> $array
     */
    public function __construct(
        array $array
    ) {
        $this->collection = collect($array);
    }
}
