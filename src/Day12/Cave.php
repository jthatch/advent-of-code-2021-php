<?php

declare(strict_types=1);

namespace App\Day12;

use Illuminate\Support\Collection;

class Cave
{
    public const START = 'start';
    public const END   = 'end';

    public readonly bool $isStart;
    public readonly bool $isEnd;
    public readonly bool $isUpper;
    public readonly bool $isSmall;

    public function __construct(public readonly string $name, public Collection $adjacent = new Collection())
    {
        $this->isStart = static::START === $name;
        $this->isEnd   = static::END   === $name;
        $this->isUpper = ctype_upper($name);
        $this->isSmall = ctype_lower($name) && !$this->isStart && !$this->isEnd;
    }

    public function traverse(Collection $totalPaths = new Collection(), array $paths = [], bool $visitSmallCaveTwice = false): Collection
    {
        $paths[] = $this->name;

        if ($this->isEnd) {
            $totalPaths->push($paths);

            return $totalPaths;
        }

        if ($visitSmallCaveTwice) {
            $smallCavesSeenTwice = collect(array_count_values($paths))->filter(fn ($c, $n) => $this->isSmallCave($n) && $c > 1)->isNotEmpty();
            $adjacent = $this->adjacent
                ->reject(static fn (Cave $cave): bool => $cave->isStart || ($cave->isSmall && in_array($cave->name, $paths, true) && $smallCavesSeenTwice));
        } else {
            $adjacent = $this->adjacent
                ->reject(static fn (Cave $cave): bool => !$cave->isUpper && in_array($cave->name, $paths, true));
        }

        $adjacent->each(static fn (Cave $cave): Collection => $cave->traverse($totalPaths, $paths, $visitSmallCaveTwice));

        return $totalPaths;
    }

    protected function isSmallCave(string $name): bool
    {
        return ctype_lower($name) && static::START !== $name && static::END !== $name;
    }
}
