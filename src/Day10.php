<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;
use Illuminate\Support\Collection;

class Day10 extends DayBehaviour
{
    protected array $tags = [
        '(' => ')',
        '[' => ']',
        '{' => '}',
        '<' => '>',
    ];

    public function solvePart1(): ?int
    {
        return collect($this->input)
            ->map(fn (string $line): ?string => $this->parseChunk($line)['errors']->first())
            ->reduce(fn (int $c, ?string $t): int => $c + match ($t) {
                ')'     => 3,
                ']'     => 57,
                '}'     => 1197,
                '>'     => 25137,
                default => 0,
            }, 0)
        ;
    }

    public function solvePart2(): ?int
    {
        return (int) collect($this->input)
            ->map(fn (string $line): array  => $this->parseChunk($line))
            ->map(fn (array $chunk): ?array => $chunk['errors']->isEmpty() ? collect($chunk['chunk'])->map(fn ($t) => $this->tags[$t])->reverse() : null)
            ->filter()
            ->map(fn ($chunk) => $chunk->reduce(fn ($c, $t) => (5 * $c) + match ($t) {')' => 1, ']' => 2, '}' => 3, '>' => 4,}, 0))
            ->median();
    }

    /**
     * @param string $line
     * @return array{chunk: array, errors: Collection}
     */
    protected function parseChunk(string $line): array
    {
        $chunk  = [];
        $errors = collect(str_split($line))
            ->filter(function (string $t) use (&$chunk) {
                if (isset($this->tags[$t])) { // we've found an open tag, add to our chunk
                    $chunk[] = $t;
                } elseif ($t === $this->tags[last($chunk)] ?? null) { // found a valid closing tag
                    array_pop($chunk);
                } else { // syntax error
                    return true;
                }

                return false;
            });

        return [
            'chunk'  => $chunk,
            'errors' => $errors,
        ];
    }

    public function example(): array
    {
        return explode(
            "\n",
            <<<eof
[({(<(())[]>[[{[]{<()<>>
[(()[<>])]({[<{<<[]>>(
{([(<{}[<>[]}>{[]{[(<()>
(((({<>}<{<{<>}{[]{[]{}
[[<[([]))<([[{}[[()]]]
[{[{({}]{}}([{[{{{}}([]
{<[[]]>}<{[{[{[]{()[[[]
[<(<(<(<{}))><([]([]()
<{([([[(<>()){}]>(<<{{
<{([{{}}[<[[[<>{}]]]>[]]
eof
        );
    }
}
