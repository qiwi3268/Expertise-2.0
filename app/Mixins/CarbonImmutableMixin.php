<?php

declare(strict_types=1);

namespace App\Mixins;

use App\Lib\Date\BusinessDater;

use Carbon\CarbonImmutable;
use App\Lib\Date\DateHelper;


final class CarbonImmutableMixin
{
    private BusinessDater $dater;

    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $this->dater = new BusinessDater;
    }


    /**
     * Возвращает смещенную дату на указанное количество рабочих дней
     *
     * @return callable
     */
    public function shiftBusinessDays(): callable
    {
        $dater = $this->dater;

        return static function (int $offset) use ($dater): CarbonImmutable {

            /** @var CarbonImmutable $date */
            $date = self::this();

            $method = $offset > 0 ? 'addDay' : 'subDay';
            $offset = abs($offset);

            while ($offset) {

                /** @var CarbonImmutable $result */
                $date = $date->{$method}();

                if (!$dater->isWeekendDay($date)) {

                    $offset--;
                }
            };
            return $date;
        };
    }


    /**
     * Возвращает дату с установленной временной зоной приложения
     *
     * @return callable
     */
    public function toAppTimeZone(): callable
    {
        $tz = DateHelper::getAppTimeZone();

        return static function () use ($tz): CarbonImmutable {

            /** @var CarbonImmutable $date */
            $date = self::this();

            return $date->setTimezone($tz);
        };
    }


    /**
     *
     *
     * @return callable
     */
    public static function validateFromFormat(): callable
    {
        return static function (string $format, string $date): bool {

            $d = CarbonImmutable::createFromFormat($format, $date);

            // Объект даты сформировался из строки без ошибки
            // и
            // Новая дата в этом же формате является исходной строкой
            // Т.к. формирование даты из строки с несуществующими днями создает
            // объект со следующим месяцем, годом и т.д.
            return $d !== false && $d->format($format) == $date;
        };

    }

}