<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;
use Closure;


/**
 * Правила сравнения
 *
 */
final class ComparisonRule
{


    /**
     * Закрытый конструктор класса
     *
     * @param Closure $closure
     */
    private function __construct(private Closure $closure)
    {}


    /**
     * Правило равенства
     *
     * @param mixed $main значение, с которым будет сравнение
     * @return self
     */
    public static function equal(mixed $main): self
    {
        return new self(function (mixed $comparable) use ($main) {
            return $comparable === $main;
        });
    }


    /**
     * Правило "выбранного" значения
     *
     * @return self
     */
    public static function on(): self
    {
        return new self(function (mixed $comparable) {
            return in_array($comparable, ['1', 1, true, 'true', 'True', 'on', 'On', 'yes', 'Yes'], true);
        });
    }


    /**
     * Правило "невыбранного" значения
     *
     * @return self
     */
    public static function off(): self
    {
        return new self(function (mixed $comparable) {
            return in_array($comparable, ['-1', -1, '0', 0, false, 'false', 'False', 'off', 'Off', 'no', 'No'], true);
        });
    }



    /**
     * Правило присутсвия значения в html массиве
     *
     * Входным параметром является html массив
     *
     * @param string $main
     * @return self
     */
    public static function inHtmlArray(string $main): self
    {
        $array = html_arr_decode($main);

        return new self(function (string $comparable) use ($array) {
            return in_array($comparable, $array, true);
        });
    }


    /**
     * Правило присутсвия значения в html массиве
     *
     * Входным параметром является значение, которое ищется в html массиве
     *
     * @param string $main
     * @return self
     */
    public static function inverseInHtmlArray(string $main): self
    {
        return new self(function (string $comparable) use ($main) {
            return in_array($main, html_arr_decode($comparable), true);
        });
    }


    /**
     * Правило присутствия в html массиве любого отличающегося значения
     *
     * Входным параметром является значение, помимо которого должны быть другие
     * в html массиве
     *
     * @param string|array $main
     * @return self
     * @throws InvalidArgumentException
     */
    public static function inverseSomethingOtherFromHtmlArray(string|array $main): self
    {
        if (is_array($main)) {
            if (!arr_is_string_list($main)) {
                throw new InvalidArgumentException('Все элементы массива должны быть строками');
            }
        } else {
            $main = [$main];
        }
        return new self(function (string $comparable) use ($main) {
            return !empty(array_diff(html_arr_decode($comparable), $main));
        });
    }


    /**
     * Сравнение
     *
     * @param mixed $comparable значение, которое сравнивается
     * @return bool
     */
    public function compare(mixed $comparable): bool
    {
        return call_user_func($this->closure, $comparable);
    }
}