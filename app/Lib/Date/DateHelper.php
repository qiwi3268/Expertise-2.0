<?php

declare(strict_types=1);

namespace App\Lib\Date;

use DateTimeImmutable;
use DateTimeZone;


/*
 * Вспомогательный класс для работы с датой
 *
 */
final class DateHelper
{

    /**
     * Возвращает временную зону приложения
     *
     * @return DateTimeZone
     */
    public static function getAppTimeZone(): DateTimeZone
    {
        $tz = required_config('app.timezone');
        return new DateTimeZone($tz);
    }


    /**
     * @deprecated
     *
     * Возвращает объект даты, приведённой к временной зоне UTC
     *
     *
     * @param DateTimeImmutable $date
     * @return DateTimeImmutable
     */
    public static function getUtcDate(DateTimeImmutable $date): DateTimeImmutable
    {
        $utc = new DateTimeZone('UTC');
        return $date->setTimezone($utc);
    }


    /**
     * @deprecated
     *
     * Валидирует дату согласно шаблону формата
     *
     * Учитывает, чтобы были указаны только существующие дни и месяцы
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validate(string $date, string $format = DateFormatter::DATETIME_FORMAT): bool
    {
        $d = DateTimeImmutable::createFromFormat($format, $date);

        // Объект даты сформировался из строки без ошибки
        // и
        // Новая дата в этом же формате является исходной строкой
        // Т.к. формирование даты из строки с несуществующими днями создает
        // объект с другим месяцем, годом и т.д.
        return $d !== false && $d->format($format) == $date;
    }
}
