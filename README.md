## Advent of Code 2021 PHP
The solutions to [advent of code 2021](https://adventofcode.com/2021), solved using PHP 8.1. By [James Thatcher](http://github.com/jthatch)

### Solutions 🥳🎉
> 🎄 [Day 1](/src/Day1.php) 🎅 [Day 2](/src/Day2.php)
### Commands
_Note: checkout the code then run `make run`. The docker and composer libraries will auto install._  

**Solve all days puzzles**  
`make run`

**Solve all days puzzles using the PEST testing framework**  
`make tests`

**Solve an individual days puzzles**  
`make run day={N}` e.g. `make run day=13`

**Solve a single part of a days puzzles**  
`make run day={N} part={N}` e.g. `make run day=16 part=2`

**Create the next days PHP files**  
_Auto detects what current Day you are on and will create the next (only if the files don't exist)_
```shell
make new
# Created new file: src/Day17.php
# Created new file: tests/Day17Test.php
```

**Fetch the next days input from the server.**  
`make get-input`  
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