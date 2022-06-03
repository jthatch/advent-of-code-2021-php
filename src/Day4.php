<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayBehaviour;

class Day4 extends DayBehaviour
{
    public function solvePart1(): ?int
    {
        [$randomNumbers, $boards] = $this->getNumbersAndBoardfromInput($this->input);

        foreach ($randomNumbers as $randomNumber) {
            foreach ($boards as &$board) {
                foreach ($board as &$boardValue) {
                    $boardNumber = key($boardValue);
                    if ($randomNumber === $boardNumber) {
                        $boardValue[$boardNumber] = 1;

                        // check if we've won
                        if ($this->hasBoardWon($board)) {
                            $sum = collect($board)
                                ->filter(fn (array $value) => 0 === array_values($value)[0])
                                ->sum(fn (array $number)   => key($number));

                            return $sum * $randomNumber;
                        }
                    }
                }
                unset($boardValue);
            }
        }
        unset($board);

        return null;
    }

    public function solvePart2(): ?int
    {
        [$randomNumbers, $boards] = $this->getNumbersAndBoardfromInput($this->input);

        $winningNumber = null;
        $winningBoard  = null;
        $boardsWon     = [];
        $totalBoards   = count($boards);
        foreach ($randomNumbers as $randomNumber) {
            foreach ($boards as $boardId => &$board) {
                foreach ($board as &$boardValue) {
                    $boardNumber = key($boardValue);
                    if ($randomNumber === $boardNumber) {
                        $boardValue[$boardNumber] = 1;

                        // check if we've won
                        if ($this->hasBoardWon($board)) {
                            $boardsWon[] = $boardId;
                            if (count($boardsWon) === $totalBoards) {
                                $winningNumber = $randomNumber;
                                $winningBoard  = $board;
                                break 3;
                            }
                            unset($boards[$boardId]);
                        }
                    }
                }
                unset($boardValue);
            }
        }
        unset($board);

        $sum = collect($winningBoard)
            ->filter(fn (array $value) => 0 === array_values($value)[0])
            ->sum(fn (array $number)   => key($number));

        return $sum * $winningNumber;
    }

    /**
     * @param array<int, string> $input
     *
     * @phpstan-return array<int, array<array<int, array<int>>|int>>
     */
    protected function getNumbersAndBoardFromInput(array $input): array
    {
        /** @var array<int> $randomNumbers */
        $randomNumbers = array_map('intval', explode(',', array_shift($input) ?? ''));
        /** @var array<int, array<int,array<int>>> $boards */
        $boards  = [];
        $boardId = -1;

        // build boards
        foreach ($input as $row) {
            if ('' === $row) {
                ++$boardId;
                $boards[$boardId] ??= [];
                continue;
            }
            if (preg_match_all('/(\d+)/', $row, $matches)) {
                /** @var array<int> $numbers */
                $numbers = array_map('intval', $matches[1]);
                /** @var int $number */
                foreach ($numbers as $number) {
                    $boards[$boardId][] = [$number => 0];
                }
            }
        }

        return [
            $randomNumbers,
            $boards,
        ];
    }

    /**
     * @param array<int, array<int>> $board
     */
    protected function hasBoardWon(array $board): bool
    {
        $boardSize = count($board);
        // vertical
        for ($i = 0; $i < 5; ++$i) {
            $sum = collect($board)
                ->nth(5, $i)
                ->sum(fn ($number) => $number[key($number)]);

            if (5 === $sum) {
                return true;
            }
        }

        // horizontal
        for ($i = 0; $i < $boardSize; $i += 5) {
            $sum = collect($board)
                ->slice($i, 5)
                ->sum(fn ($number) => $number[key($number)]);

            if (5 === $sum) {
                return true;
            }
        }

        return false;
    }
}
