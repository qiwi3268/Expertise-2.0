<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Dates;

use App\Exceptions\Lib\FormHandling\FormLogicException;

use App\Lib\FormHandling\Units\Items\FormItem;
use App\Lib\Date\DateHelper;
use App\Lib\Date\DateFormatter;
use DateTimeImmutable;


/**
 * Айтем, характеризующий дату без времени
 *
 */
abstract class DateItem extends FormItem
{

    private DateTimeImmutable $date;


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     */
    protected function validate(string $value): bool
    {
        if (DateHelper::validate($value, 'd.m.Y')) {

            $date = DateTimeImmutable::createFromFormat('d.m.Y H', "{$value} 00");

            if ($this->doValidate($date)) {

                $this->date = $date;
                return true;
            }
        }
        return false;
    }


    /**
     * Преобразует значение к формату БД
     *
     * @param string|null $value
     * @return string|null
     */
    public function valueMutator(?string $value): ?string
    {
        return $this->isFilled()
            ? $this->getDate()->format(DateFormatter::DATE_FORMAT)
            : $value;
    }


    /**
     * Возвращает объект даты
     *
     * @return DateTimeImmutable
     * @throws FormLogicException
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date ??
            throw new FormLogicException('Объект даты не инициализирован');
    }


    /**
     * Валидирует полученную дату
     *
     * @param DateTimeImmutable $date
     * @return bool
     */
    abstract protected function doValidate(DateTimeImmutable $date): bool;
}