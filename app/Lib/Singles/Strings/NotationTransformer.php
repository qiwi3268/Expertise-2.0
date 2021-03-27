<?php

declare(strict_types=1);

namespace App\Lib\Singles\Strings;

use InvalidArgumentException;

use Illuminate\Support\Str;


/**
 * Преобразует строку к различным нотациям
 *
 * Преобразуемая строка изначально может быть конкатенирована из нескольких строк
 */
final class NotationTransformer
{

    private array $parts;

    /**
     * Части строки, склеенные пробелом
     *
     * Пробел обеспечивает корректную работу laravel функций
     */
    private string $glued;


    /**
     * Конструктор класса
     *
     * @param string|null ...$parts части преобразуемой строки
     * @throws InvalidArgumentException
     */
    public function __construct(?string ...$parts)
    {
        $emptyCount = 0;

        foreach ($parts as &$part) {

            if (is_null($part)) {
                $part = '';
                $emptyCount++;
            } elseif ($part === '') {
                $emptyCount++;
            }
        }
        unset($part);

        if ($emptyCount == count($parts)) {
            throw new InvalidArgumentException('Отсутствуют непустые части строки');
        }
        $this->parts = $parts;
        $this->glued = implode(' ', $parts);
    }


    /**
     * Возвращает numeric_snake строку
     *
     * @return string
     */
    public function toNumericSnake(): string
    {
        return (string) Str::numericSnake($this->glued);
    }


    /**
     * Возвращает camel строку
     *
     * @return string
     */
    public function toCamel(): string
    {
        return (string) Str::camel($this->glued);
    }
}