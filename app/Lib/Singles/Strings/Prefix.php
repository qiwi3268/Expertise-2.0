<?php

declare(strict_types=1);

namespace App\Lib\Singles\Strings;

use InvalidArgumentException;


/**
 * ООП интерфейс добавления префикса к строке
 *
 */
final class Prefix
{

    /**
     * Конструктор класса
     *
     * @param string $prefix
     * @throws InvalidArgumentException
     */
    public function __construct(private string $prefix)
    {
        if (empty($prefix)) {

            throw new InvalidArgumentException('Префикс не может быть пустым');
        }
    }


    /**
     * Возвращает строку с префиксом
     *
     * @param string $value
     * @return string
     * @throws InvalidArgumentException
     */
    public function __invoke(string $value): string
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Строка, добавляемая к префиксу, не может быть пустой');
        }
        return "{$this->prefix}{$value}";
    }


    /**
     * Предназначен для слияния префиксов в один
     *
     * @param string $prefix
     * @param self ...$mains перечисление объектов, в которые будет обернут префикс
     * @return self
     */
    public static function merge(string $prefix, self...$mains): self
    {
        for ($f = (count($mains) - 1); $f >= 0; $f--) { // Обратный цикл

            $prefix = $mains[$f]($prefix);
        }
        return new self($prefix);
    }


    /**
     * Возвращает префикс
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}