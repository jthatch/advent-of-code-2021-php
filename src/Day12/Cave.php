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

    public function traverse(int $totalPaths = 0, array $paths = [], bool $visitSmallCaveTwice = false): int
    {
        if ($this->isEnd) {
            return ++$totalPaths;
        }

        $paths[] = $this->name;

        if ($visitSmallCaveTwice) { // refactored without collect() for speed optimisations (2x faster)
            $smallCavesSeenTwice = !empty(array_filter(array_count_values($paths), fn ($c, $n) => $c > 1 && $this->isSmallCave($n), ARRAY_FILTER_USE_BOTH));
            foreach ($this->adjacent as $cave) {
                if ($cave->isStart || ($cave->isSmall && $smallCavesSeenTwice && in_array($cave->name, $paths, true))) {
                    continue;
                }

                $totalPaths = $cave->traverse($totalPaths, $paths, $visitSmallCaveTwice);
            }
        } else {
            $this->adjacent
                ->reject(static fn (Cave $cave): bool => !$cave->isUpper && in_array($cave->name, $paths, true))
                ->each(static function (Cave $cave) use (&$totalPaths, $paths, $visitSmallCaveTwice) {
                    $totalPaths = $cave->traverse($totalPaths, $paths, $visitSmallCaveTwice);
                });
        }

        return $totalPaths;
    }

    protected function isSmallCave(string $name): bool
    {
        return ctype_lower($name) && static::START !== $name && static::END !== $name;
    }
}
