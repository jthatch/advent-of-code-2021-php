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

    public function traverse(int $totalPaths = 0, array $paths = [], bool $visitSmallCaveTwice = false, bool $smallCavesSeenTwice = false): int
    {
        if ($this->isEnd) {
            return ++$totalPaths;
        }

        $paths[] = $this->name;

        // this is the most expensive code (apart from collect stuff below lol) so once true we avoid re-calculating and pass to subsequent traverse()'s as an argument
        if ($visitSmallCaveTwice && !$smallCavesSeenTwice) {
            $smallCavesSeenTwice = !empty(array_filter(array_count_values($paths), fn (int $c, string $n): bool => $c > 1 && $this->isSmallCave($n), ARRAY_FILTER_USE_BOTH));
        }

        return (!$visitSmallCaveTwice // using ternary instead of collect's when() is much faster.
            ? $this->adjacent // part 1
                ->reject(fn (Cave $cave): bool => !$cave->isUpper && in_array($cave->name, $paths, true))
            : $this->adjacent // part 2
                ->reject(fn (Cave $cave): bool => $cave->isStart || ($cave->isSmall && $smallCavesSeenTwice && in_array($cave->name, $paths, true)))
        )->reduce(fn (int $carry, Cave $cave): int => $carry + $cave->traverse($totalPaths, $paths, $visitSmallCaveTwice, $smallCavesSeenTwice), 0);
    }

    protected function isSmallCave(string $name): bool
    {
        return ctype_lower($name) && static::START !== $name && static::END !== $name;
    }
}
