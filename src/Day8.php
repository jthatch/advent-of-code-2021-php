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
                $digitsByLength = collect($signal)->flatten()->mapToGroups(fn ($s) => [strlen($s) => collect(str_split($s))->sort()]);

                // set digits 1,4,7,8 which have unique segment lengths 2,4,3,7 respectively
                $digits[1] = $digitsByLength->get(2)->first();
                $digits[4] = $digitsByLength->get(4)->first();
                $digits[7] = $digitsByLength->get(3)->first();
                $digits[8] = $digitsByLength->get(7)->first();

                // now work out digits 0, 6 and 9 which share a segment length of 6
                $digits[6] = $digitsByLength->get(6)
                    ->filter(function ($s) use (&$digits) {
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
                $digits[2] = $digitsByLength->get(5)
                    ->filter(function ($s) use (&$digits) {
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

                // sort our output patterns (bcd becomes cbd) to match the signals and determine the output value
                return $carry + (int) collect($output)
                    ->map(fn ($s) => collect(str_split($s))->sort()->join(''))
                    ->map(fn ($s) => $digits->get($s))
                    ->join('');
            }, 0);
    }

    /**
     * Part 2 Notes (this was a hard one).
     *
     * The wires are mixed up, for example instead of 7 being "acf" it will be "dab", likewise 1 should be "cf" but it's "ab"
     * We can find 7 and 1 by looking at the length of the segments, same for numbers 4 and 8. these all have unique lengths:
     * len=2 no=1
     * len=3 no=7
     * len=4 no=4
     * len=7 no=8
     *
     * The only digit we can determine with absolute certainty is "a" by see what digit is present in 7 but not 1.
     * "dab" (7) diff "ab" (1), therefore a = d, c is either [a, b]
     * Here are the rules for determining certain wires based on the unique digit lengths:
     *  a = 7 diff 1
     *  b = in [4]
     *  c = in [1,4,7]
     *  d = in [4]
     *  f = in (1,4,7]
     *
     *  1) The 10 signal patterns are unique, and the order is important, so sorting will prevent distinguishing two digits
     *     that share the same length, E.G, from the example: 6 (cdfgeb) and 9 (cefabd).
     *  2) We should solve this by assigning each wire (abcdefg) a set of possible wires E.G. (a -> b,c,d,e,f,g).
     *  3) Digits 1, 4, 7, 8 have unique segment lengths, allowing us to reduce the number of possible wires for these.
     */
    public function solvePart2Older(): ?int
    {
        $wires    = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];
        $segments = [
            0 => 'abcefg',
            1 => 'cf',      // only one with 2 characters
            2 => 'acdeg',
            3 => 'acdfg',
            4 => 'bcdf',    // only one with 4 characters
            5 => 'abdfg',
            6 => 'abdefg',
            7 => 'acf',     // only one with 3 characters, "a" can be found by: diff 7 ("acf") and 1 ("cf")
            8 => 'abcdefg', // only one with 7 characters
            9 => 'abcdfg',
        ];
        $map = [
            'a' => 'd',
            'b' => 'e',
            'c' => 'a',
            'd' => 'f',
            'e' => 'g',
            'f' => 'b',
            'g' => 'c',
        ];
        $segmentSort = fn (string $s) => collect(str_split($s))->sort()->join('');
        $segmentMapped = fn (string $s)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => collect(str_split($s))->map(fn ($s)                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  => ($k = array_search($s, $map, true)) ? $k : $s)->join('');
        $isSegmentValid                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 = fn (string $s) => array_search($s, $segments, true);

        $foo = 'cdfbe';
        $bar = $segmentMapped($foo);
        $baz = $segmentSort($bar);

        $a = $isSegmentValid($bar);
        $b = $isSegmentValid($baz);

        $this->input = ['acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf'];

        $input = collect($this->input)
            // each line comprises of ten unique signal patterns, a | delimiter, and finally the four digit output value
            ->map(fn (string $line) => explode(' | ', $line))
            // $a = array of signal patterns, $b = array of output values
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
            // iterate over each row
            ->map(function (array $ab) use ($segmentSort, $isSegmentValid): void {
                $row   = collect($ab)->flatten();
                $known = $row
                    ->filter(fn ($s) => in_array(strlen($s), [2, 4, 3, 7]))
                    ->toArray();

                $f   = 1;
                $row = collect($ab)->flatten()
                    // ->map($segmentMapped)
                    ->map($segmentSort)
                    ->each(function ($s) use ($isSegmentValid): void {
                        $v = $isSegmentValid($s);
                        $f = 1;
                    });
            });

        $f = 1;

        $line    = 'acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf';
        $lineNew = collect(str_split($line))
            ->map(fn ($s) => $mapFlipped[$s] ?? null ? $mapFlipped[$s] : $s)->join('');
        $foo           = explode(' | ', $lineNew);
        $lineNewSorted = collect(explode(' | ', $lineNew))
            ->sliding(2)
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
        // make the digits in order a-g
        ->mapSpread(fn (array $a, array $b) => [
            collect($a)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
            collect($b)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
        ])
        ->flatten()
            ->join(' ');
        /*
         acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf
         cgbaedf gadfb egadc dfgca acf gbdcfa gadebf bcdf gcebaf cf | gadbf dgcaf gadbf gafcd
         abcdefg abdfg acdeg acdfg acf abcdfg abdefg bcdf abcefg cf | abdfg acdfg abdfg acdfg

         */
        printf(
            "cdfeb = %s gadbf = %s abdfg = %s\n",
            collect(str_split('cdfeb'))->map(fn ($s) => ord($s))->sum(),
            collect(str_split('gadbf'))->map(fn ($s) => ord($s))->sum(),
            collect(str_split('abdfg'))->map(fn ($s) => ord($s))->sum(),
        );

        $digits = collect([
            0 => 'abcefg',
            1 => 'cf',      // only one with 2 characters
            2 => 'acdeg',
            3 => 'acdfg',
            4 => 'bcdf',    // only one with 4 characters
            5 => 'abdfg',
            6 => 'abdefg',
            7 => 'acf',     // only one with 3 characters
            8 => 'abcdefg', // only one with 7 characters
            8 => 'acedgfb', // only one with 7 characters
            9 => 'abcdfg',
            9 => 'cefabd',
        ])
            ->each(fn ($s) => printf(
                "%10s = %d\n",
                $s,
                collect(str_split($s))->map(fn ($s) => ord($s))->sum(),
            ));

        printf("%s\n", $line);
        printf("%s\n", $lineNew);
        printf("%s\n", $lineNewSorted);

        return null;
    }

    /**
     * @deprecated
     *
     * @return int|null
     */
    public function solvePart2Old(): ?int
    {
        // build a collection of digits and their segment counterparts
        // as segments 1,4,7,8 have unique lengths, we can use this to match the randomised segments in the input
        $digits = collect([
            0 => 'abcefg',
            1 => 'cf',      // only one with 2 characters
            2 => 'acdeg',
            3 => 'acdfg',
            4 => 'bcdf',    // only one with 4 characters
            5 => 'abdfg',
            6 => 'abdefg',
            7 => 'acf',     // only one with 3 characters
            8 => 'abcdefg', // only one with 7 characters
            8 => 'acedgfb', // only one with 7 characters
            9 => 'abcdfg',
            9 => 'cefabd',
        ])
        ->map(
            fn (string $s, int $n) => [
                'number'   => $n,
                'segments' => $s,
                'length'   => strlen($s), ]
        );

        // build an index of which segments are in which digits, for example
        // segment `f` is in every number except 2
        $segments = collect(['a', 'b', 'c', 'd', 'e', 'f', 'g'])
            ->flip()
            ->map(fn ($v, $s) => [
                'in'    => $digits->filter(fn (array $d)    => str_contains($d['segments'], $s))->pluck('number')->toArray(),
                'notIn' => $digits->filter(fn (array $d) => !str_contains($d['segments'], $s))->pluck('number')->toArray(),
            ]);

        // $segmentsByLength = collect($this->display())->mapToGroups(fn ($d, $k) => [strlen($d) => [$k => $d]])->map(fn ($d) => $d->mapWithKeys(fn ($d, $k) => $d))->toArray();
        $digitsByLength = collect($this->display())->mapToGroups(fn ($d, $k) => [strlen($d) => $k])->toArray();

        $this->input = $this->example();
        $input       = collect($this->input)
            // each line comprises of ten unique signal patterns, a | delimiter, and finally the four digit output value
            ->map(fn (string $line) => explode(' | ', $line))
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
            // make the digits in order a-g
            ->mapSpread(fn (array $a, array $b) => [
                collect($a)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
                collect($b)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
            ])
            // each row is randomly mixed up
            ->map(function (array $ab) use ($digitsByLength, $segments): void {
                $numbers = collect()->pad(10, [])->toArray();
                $row     = collect($ab)->flatten()
                    ->each(function ($s) use (&$numbers, $digitsByLength): void {
                        // $blah = collect($digitsByLength[strlen($s)])->flip()->map(fn($v) => $s);
                        collect($digitsByLength[strlen($s)])->each(
                            function ($n) use (&$numbers, $s): void {
                                $numbers[$n][] = $s;
                            }
                        );
                    });
                // number 7 (originally "acf") diffed by number 1 (originally "cf") gives us the letter "a"
                $a = collect(str_split($numbers[7][0]))->diff(str_split($numbers[1][0]))->join('');
                collect($segments->get('a')['notIn'])
                    ->each(function ($n) use (&$numbers, $a): void {
                        $f           = 1;
                        $numbers[$n] = collect($numbers[$n])
                            ->reject(fn ($s) => str_contains($s, $a))
                            ->toArray();
                        $f = 1;
                    });
                collect($segments->get('a')['in'])
                    ->each(function ($n) use (&$numbers, $a): void {
                        $f           = 1;
                        $numbers[$n] = collect($numbers[$n])
                            ->filter(fn ($s) => str_contains($s, $a))
                            ->toArray();
                        $f = 1;
                    });
                // build an array of potential segments indexed by number (0-9).
                // numbers 1,4,7,8 will have a single segment, the rest will contain multiple which we have to whittle down
                // $row = collect($ab)->flatten()->mapToGroups(fn ($s) => [$s => array_keys($segmentsByLength[strlen($s)])])->map(fn ($d) => $d->mapWithKeys(fn ($d) => $d));
                // todo: build an array of numbers [7]['bde']

                // number 7 (originally "acf") diffed by number 1 (originally "cf") gives us the letter "a"
                // $a = collect(str_split($row->get(7)->first()))->diff(str_split($row->get(1)->first()))->join('');
                // now we know what "a" is, we can remove from row
                // $row = $row->where
                // collect($segments->get('a')['notIn'])
                $f = 1;
            });

        return null;
    }

    public function solvePart2Old2(): ?int
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
        $digitsIn = collect($map)
            ->map(fn ($v, $n) => [
                'in'    => collect($this->display())->filter(fn ($v)    => str_contains($v, $n))->toArray(),
                'notIn' => collect($this->display())->filter(fn ($v) => !str_contains($v, $n))->toArray(),
            ])

            ->toArray();
        $f           = 1;
        $mapOfDigits = collect($this->display())->mapToGroups(fn ($d, $k) => [strlen($d) => [$k => $d]])->map(fn ($d) => $d->mapWithKeys(fn ($d, $k) => $d))->toArray();
        $this->input = $this->example();
        $input       = collect($this->input)
            // each line comprises of ten unique signal patterns, a | delimiter, and finally the four digit output value
            ->map(fn (string $line) => explode(' | ', $line))
            ->mapSpread(fn (string $a, string $b) => [explode(' ', $a, 10), explode(' ', $b, 4)])
            // make the digits in order a-g
            ->mapSpread(fn (array $a, array $b) => [
                collect($a)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
                collect($b)->map(fn ($s) => collect(str_split($s))->sort()->join(''))->toArray(),
            ])
            // each row is randomly mixed up
            ->map(function (array $ab) use ($mapOfDigits): void {
                $knownDigits = collect($ab)
                    ->flatten()
                    ->filter(fn ($v) => in_array(strlen($v), [2, 4, 3, 7]))
                    ->mapWithKeys(
                        fn ($s) => [key($mapOfDigits[strlen($s)]) => str_split($s)]
                    );
                $a          = collect($knownDigits->get(7))->diff($knownDigits->get(1))->first();
                $digitCanBe = collect($ab)->flatten()
                    ->mapToGroups(fn ($d, $k) => [$d => array_keys($mapOfDigits[strlen($d)])])->map(fn ($d) => $d->mapWithKeys(fn ($d, $k) => $d))
                    ;
                $digitsMod = collect($digitCanBe->toArray())->filter(
                    fn ($canBe, $d) => str_contains($canBe, $a)
                )
                    ->toArray();
                // collect(array_keys($digitsIn['a']['in']))->
                // filter $digitsCanBe['a']['in'] using $
                $all = collect($ab)->flatten()
                    ->flatMap(
                    // fn ($s, $k) => [$s => 1 === count($mapOfDigits[strlen($s)] ?? []) ? $mapOfDigits[strlen($s)] : null]
                        fn ($s, $k) => [$s => $mapOfDigits[strlen($s)]]
                    );
                // a - diff between 7,1
                // $map['a'] = collect($knownDigits->get(7))->diff($knownDigits->get(1))->first();
                // $foo      = collect($knownDigits->get(4))->diff($knownDigits->get(7))->toArray();
                // $unknown  = collect($ab)->flatten()->reject(fn ($v) => in_array(strlen($v), [2, 4, 3, 7]))->toArray();
                $f = 1;
            })
        ;

        $foo = 1;

        return (int) $input;
    }

    /**
     * @return string[]
     */
    protected function display(): array
    {
        $sizes = [
            2 => [1],
            4 => [4],
            3 => [7],
            7 => [8],

            6 => [],
        ];

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

    protected function diffBetween(int $numberA, int $numberB): ?array
    {
        $foo = collect($this->display())->map(fn ($s) => str_split($s));

        $diff = [
            'a' => fn ($a, $b) => $this->diffBetween(7, 1),
            'b' => fn ($a, $b) => $this->diffBetween(5, 3),
            'd' => '',
        ];

        return collect($foo->get($numberA))->diff($foo->get($numberB))->toArray();
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
