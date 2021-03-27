<?php

declare(strict_types=1);

namespace App\Lib\Date;

use App\Repositories\Calendars\CalendarWorkdayRepository;
use App\Repositories\Calendars\CalendarHolidayRepository;

use App\Lib\Singles\Arrays\HashArray;

use DateTimeInterface;
use stdClass;


/**
 * Независимый класс, обеспечивающий определение выходного дня
 *
 */
final class BusinessDater
{
    /**
     * Праздничные пн/вт/ср/чт/пт
     */
    private static HashArray $holidays;

    /**
     * Рабочие сб/вс
     */
    private static HashArray $workdays;


    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        if (!isset(self::$holidays)) {

            self::$holidays = HashArray::createByCallback(
                (new CalendarHolidayRepository)->getAllDates(), fn (stdClass $o) => $o->date
            );
            self::$workdays = HashArray::createByCallback(
                (new CalendarWorkdayRepository)->getAllDates(), fn (stdClass $o) => $o->date
            );
        }
    }


    /**
     * Является ли дата выходным днем
     *
     * Праздничные пн/вт/ср/чт/пт и нерабочие сб/вс
     *
     * @param DateTimeInterface $date
     * @return bool
     */
    public function isWeekendDay(DateTimeInterface $date): bool
    {
        $ymd = $date->format('Y-m-d');

        return $date->format('N') >= 6 // Сб, вс
            ? self::$workdays->missing($ymd)
            : self::$holidays->has($ymd);
    }
}