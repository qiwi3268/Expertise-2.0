<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Dates;

use DateTime;
use DateTimeImmutable;


/**
 * Будущая или сегодняшняя дата
 *
 */
final class FutureDate extends DateItem
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
        return $diff <= 0;
    }
}