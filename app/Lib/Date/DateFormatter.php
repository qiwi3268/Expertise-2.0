<?php

declare(strict_types=1);

namespace App\Lib\Date;

use InvalidArgumentException;
use DateTimeImmutable;


final class DateFormatter
{

    /**
     * Формат БД DATETIME
     */
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Формат БД DATE
     */
    public const DATE_FORMAT = 'Y-m-d';

    public const d_m_Y_FORMAT = 'd.m.Y';


    /**
     * Конструктор класса
     *
     * @param string $inputFormat
     * @param string $outputFormat
     */
    public function __construct(
        private string $inputFormat,
        private string $outputFormat
    ) {}


    /**
     * Быстрый вызов метода format
     *
     * @param string $inputDate
     * @return string
     */
    public function __invoke(string $inputDate): string
    {
        return $this->format($inputDate);
    }


    /**
     * Возвращает дату, отформатированную согласно переданному формату
     *
     * @param string $inputDate
     * @return string
     * @throws InvalidArgumentException
     */
    public function format(string $inputDate): string
    {
        $date = DateTimeImmutable::createFromFormat($this->inputFormat, $inputDate);

        if ($date === false) {
            throw new InvalidArgumentException("Ошибка при создании объекта даты из строки: '{$inputDate}' с форматом: '{$this->inputFormat}'");
        }
        return $date->format($this->outputFormat);
    }
}