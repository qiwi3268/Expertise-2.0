<?php

declare(strict_types=1);

namespace App\Lib\Singles\Strings;

use InvalidArgumentException;


/**
 * ООП интерфейс для функции sprintf
 *
 */
final class Sprintf
{

    /**
     * Конструктор класса
     *
     * @param string $format
     * @throws InvalidArgumentException
     */
    public function __construct(private string $format)
    {
        if (empty($format)) {
            throw new InvalidArgumentException('Строка формата не может быть пустой');
        }
    }


    /**
     * Возвращает отформатированную строку
     *
     * @param string ...$values
     * @return string
     */
    public function __invoke(string ...$values): string
    {
        return vsprintf($this->format, $values);
    }
}