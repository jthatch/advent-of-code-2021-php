<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day10 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $tags = [
            '(' => ')',
            '[' => ']',
            '{' => '}',
            '<' => '>',
        ];

        return collect($this->input)
            ->map(
                function (string $line) use ($tags) {
                    $tree = [];

                    $errors = collect(str_split($line))
                        ->filter(function (string $t) use ($tags, &$tree) {
                            $isOpenTag = isset($tags[$t]);
                            if ($isOpenTag) {
                                $tree[] = $t;
                            } else {
                                $lastOpen   = last($tree);
                                $isValidTag = $t === $tags[$lastOpen];
                                if ($isValidTag) {
                                    array_pop($tree);
                                } else {
                                    return true;
                                }
                            }

                            return false;
                        });

                    return $errors->isNotEmpty() ? $errors->first() : null;
                }
            )
            ->filter()
            ->reduce(fn ($c, $t) => $c + match ($t) {
                ')' => 3,
                ']' => 57,
                '}' => 1197,
                '>' => 25137,
            }, 0)
        ;
    }

    public function solvePart2(): ?int
    {
        // TODO: Implement solvePart2() method.
        return null;
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
