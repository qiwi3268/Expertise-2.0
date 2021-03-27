<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Dates;

use DateTime;
use DateTimeImmutable;


/**
 * Прошедшая или сегодняшняя дата
 *
 */
final class PastDate extends DateItem
{

    /**
     * Реализация абстрактного метода
     *
     * @param DateTimeImmutable $date
     * @return bool
     */
    protected function doValidate(DateTimeImmutable $date): bool
    {
        $now = (new DateTime)->setTime(0,0,0,0);
        // Разница в абсолютном количестве дней со знаком
        $diff = (int) $date->diff($now)->format('%R%a');
        return $diff >= 0;
    }
}