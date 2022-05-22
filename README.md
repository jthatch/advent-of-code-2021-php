## Advent of Code 2021 PHP
The solutions to [advent of code 2021](https://adventofcode.com/2021), solved using PHP 8.1. By [James Thatcher](http://github.com/jthatch)

### Solutions 🥳🎉
> 🎄 [Day 1](/src/Day1.php) 🎅 [Day 2](/src/Day2.php) ☃️ [Day 3](/src/Day3.php) 🦌 [Day 4](/src/Day4.php) 🍪 [Day 5](/src/Day5.php)
> 🥛 [Day 6](/src/Day6.php) 🧦 [Day 7](/src/Day7.php) 🎁 [Day 8](/src/Day8.php)
> ⛄ [Day 9](/src/Day9.php)
### About
My attempts at tacking the awesome challenges at [Advent of Code 2021](https://adventofcode.com/2021/day/1) using PHP 8.1.

Unlike my solutions for [AOC 2020](https://github.com/jthatch/advent-of-code-php-2020), where I used `array_*` 
functions and aimed for maximum efficiency, this year I'm using the excellent 
[Collections](https://laravel.com/docs/9.x/collections) package that comes with Laravel. This lets me do common
array manipulation without effort, and not losing much in the way of performance. Although as you'll see, I still love
a good recursive function 😉

![day runner in action](/aoc-2021-jthatch-in-action.png "AOC 2021 PHP by James Thatcher")

### Commands
_Note: checkout the code then run `make run`. The docker and composer libraries will auto install._  

**Solve all days puzzles**  
`make run`

**Solve an individual days puzzles**  
`make run day={N}` e.g. `make run day=13`

**Solve a single part of a days puzzles**  
`make run day={N} part={N}` e.g. `make run day=16 part=2`

**Create the next days PHP files**  
_Auto detects what current Day you are on and will create the next (only if the files don't exist)_
```shell
make new
# Created new file: src/Day8.php
```

**Fetch the next days input from the server.**  
```shell
make get-input
# Fetching latest input using day=8 AOC_COOKIE=53616c7465645f5f539435aCL1P
./input/day8.txt downloaded
```  
_Note: The Makefile reads the [/src](/src) directory to find the most recent DayN.php file. If you had just completed `Day1.php` you would create a `Day2.php` (by running `make new`) and then run this command to fetch `/input/day2.txt`_

**Use XDebug**  
`make xdebug`  

**Xdebug can also be triggered on a single days and/or part**  
`make xdebug day={N}` e.g. `make xdebug day=13` or `make xdebug day=13 part=2`

IDE settings:
- `10000` - xdebug port 
- `aoc-2021` - PHP_IDE_CONFIG (what you put in PHPStorm -> settings -> debug -> server -> name)
- `/app` - absolute path on the server  
- see [xdebug.ini](/xdebug.ini) if you're stuck