<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day8 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        $input = collect($this->input)
            // each line comprises of ten unique signal patterns, a | delimiter, and finally the four digit output value
            ->map(fn (string $line) => explode(' | ', $line))
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
            // to solve part1, just return just the $b row (output value)
            ->map(fn (array $ab) => $ab[1])
            // count occurrences of 1,4,7,8 which have string lengths 2,4,3,7 respectively
            ->reduce(
                fn (int $carry, array $b) => collect($b)->reduce(
                    fn (int $c, string $v) => (in_array(strlen($v), [2, 4, 3, 7]) ? 1 : 0) + $c,
                    0
                ) + $carry,
                0
            );

        return (int) $input;
    }

    public function solvePart2(): ?int
    {
        $map = [
            'a' => null,
            'b' => null,
            'c' => null,
            'd' => null,
            'e' => null,
            'f' => null,
            'g' => null,
        ];
        $this->input = $this->example();
        $input       = collect($this->input)
            // each line comprises of ten unique signal patterns, a | delimiter, and finally the four digit output value
            ->map(fn (string $line) => explode(' | ', $line))
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
            ->map(function (array $ab): void {
                $known = collect($ab)->flatten()->filter(fn ($v) => in_array(strlen($v), [2, 4, 3, 7]))->toArray();
                $f = 1;
            })
            // sort the strings so they rank a-z
            /*->mapSpread(fn (array $a, array $b) => [
                collect($a)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
                collect($b)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
            ])
            ->mapSpread(fn ($a, $b) =>
                [
                    collect($a)->filter(fn($s) => strlen($s) === 2)->toArray(),
                    collect($b)->filter(fn($s) => strlen($s) === 2)->toArray(),
                ]
            )
            ->mapSpread(fn ($a, $b) =>
                $f = 1
            )*/
        ;

        $foo = 1;

        return (int) $input;
    }

    /**
     * @return string[]
     */
    protected function display(): array
    {
        return [
            0 => 'abcefg',
            1 => 'cf',      // only one 2 characters
            2 => 'acdeg',
            3 => 'acdfg',
            4 => 'bcdf',    // only one 4 characters
            5 => 'abdfg',
            6 => 'abdefg',
            7 => 'acf',     // only one 3 characters
            8 => 'abcdefg', // only one 7 characters
            9 => 'abcdfg',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function example(): array
    {
        return explode("\n", <<<eof
be cfbegad cbdgef fgaecd cgeb fdcge agebfd fecdb fabcd edb | fdgacbe cefdb cefbgd gcbe
edbfga begcd cbg gc gcadebf fbgde acbgfd abcde gfcbed gfec | fcgedb cgb dgebacf gc
fgaebd cg bdaec gdafb agbcfd gdcbef bgcad gfac gcb cdgabef | cg cg fdcagb cbg
fbegcd cbd adcefb dageb afcb bc aefdc ecdab fgdeca fcdbega | efabcd cedba gadfec cb
aecbfdg fbg gf bafeg dbefa fcge gcbea fcaegb dgceab fcbdga | gecf egdcabf bgf bfgea
fgeab ca afcebg bdacfeg cfaedg gcfdb baec bfadeg bafgc acf | gebdcfa ecba ca fadegcb
dbcfg fgd bdegcaf fgec aegbdf ecdfab fbedc dacgb gdcebf gf | cefg dcbef fcge gbcadfe
bdfegc cbegaf gecbf dfcage bdacg ed bedf ced adcbefg gebcd | ed bcgafe cdgba cbgef
egadfb cdbfeg cegd fecab cgb gbdefca cg fgcdab egfdb bfceg | gbdfcae bgc cg cgb
gcafb gcf dcaebfg ecagb gf abcdeg gaef cafbge fdbac fegbdc | fgae cfgab fg bagce
eof);
    }
}
