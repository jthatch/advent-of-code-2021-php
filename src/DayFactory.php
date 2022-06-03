<?php

declare(strict_types=1);

namespace App;

use App\Contracts\DayInterface;
use App\Exceptions\DayClassNotFoundException;
use App\Exceptions\DayInputNotFoundException;

class DayFactory
{
    protected const MAX_DAYS     = 25;
    protected const CLASS_FORMAT = 'Day%d';
    protected const INPUT_FORMAT = __DIR__.'/../input/day%d.txt';

    /**
     * @throws DayInputNotFoundException|DayClassNotFoundException
     */
    public static function create(int $dayNumber): DayInterface
    {
        /** @phpstan-var class-string<DayInterface> **/
        $dayClassName = self::getDayClass($dayNumber);
        $dayInputName = self::getDayInput($dayNumber);

        $dayInput = file_exists($dayInputName)
            ? file($dayInputName, FILE_IGNORE_NEW_LINES)
            : throw new DayInputNotFoundException("Input file not found: {$dayInputName}", $dayNumber);
        if (!class_exists($dayClassName)) {
            throw new DayClassNotFoundException("Missing day class: {$dayClassName}");
        }

        return new $dayClassName($dayInput);
    }

    public static function allAvailableDays(): \Generator
    {
        foreach (range(1, static::MAX_DAYS) as $dayNumber) {
            try {
                yield static::create($dayNumber);
            } catch (\Exception|\Error) {
                break; // ignore days we haven't solved yet
            }
        }
    }

    /**
     * @return class-string<DayInterface>|string
     */
    private static function getDayClass(int $dayNumber): string
    {
        return __NAMESPACE__.'\\'.sprintf(static::CLASS_FORMAT, $dayNumber);
    }

    private static function getDayInput(int $dayNumber): string
    {
        return sprintf(static::INPUT_FORMAT, $dayNumber);
    }
}
