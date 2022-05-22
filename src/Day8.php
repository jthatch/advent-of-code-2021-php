<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day8 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        return (int) collect($this->input)
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
    }

    public function solvePart2(): ?int
    {
        return (int) collect($this->input)
            // each line comprises of ten unique signal patterns, a | delimiter, and finally the four digit output value
            ->map(fn (string $line) => explode(' | ', $line))
            // $a = array of signal patterns, $b = array of output values
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
            ->reduce(function (int $carry, array $entry) {
                [$signal, $output] = $entry;
                // group digits by segment length
                $digitsByLength = collect($signal)->mapToGroups(fn ($s) => [strlen($s) => collect(str_split($s))->sort()]);

                // set digits 1,4,7,8 which have unique segment lengths 2,4,3,7 respectively
                $digits[1] = $digitsByLength->get(2)->first();
                $digits[4] = $digitsByLength->get(4)->first();
                $digits[7] = $digitsByLength->get(3)->first();
                $digits[8] = $digitsByLength->get(7)->first();

                // now work out digits 0, 6 and 9 which share a segment length of 6
                $digits[6] = $digitsByLength->get(6)->filter(function ($s) use (&$digits) {
                    // digit 9 has 4 segments in common with digit 4 (b,c,d,f)
                    if (4 === $s->intersect($digits[4])->count()) {
                        $digits[9] = $s;

                        return false;
                    }

                    // digit 0 has 2 segments in common with digit 1 (c,f)
                    if (2 === $s->intersect($digits[1])->count()) {
                        $digits[0] = $s;

                        return false;
                    }

                    return true;
                })
                // digit 6 must be whatever remains
                ->first();

                // finally, work out digits 2, 3 and 5 which share a segment length of 5
                $digits[2] = $digitsByLength->get(5)->filter(function ($s) use (&$digits) {
                    // digit 3 has 2 segments in common with digit 1 (c,f)
                    if (2 === $s->intersect($digits[1])->count()) {
                        $digits[3] = $s;

                        return false;
                    }

                    // digit 5 has 3 segments in common with digit 4 (b,d,f)
                    if (3 === $s->intersect($digits[4])->count()) {
                        $digits[5] = $s;

                        return false;
                    }

                    return true;
                })
                // finally, digit 2 must be whatever remains
                ->first();

                // remap our digits to [signal -> digit]
                $digits = collect($digits)->mapWithKeys(fn ($s, $k) => [$s->join('') => $k]);

                // sort our output patterns (cbd becomes bcd) to match the signals and determine the output value
                return $carry + (int) collect($output)
                    ->map(fn ($s) => collect(str_split($s))->sort()->join(''))
                    ->map(fn ($s) => $digits->get($s))
                    ->join('');
            }, 0);
    }

    /**
     * @return string[]
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
