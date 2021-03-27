<?php

declare(strict_types=1);

namespace App\Lib\Singles\Arrays;

use InvalidArgumentException;


/**
 * Обеспечивает доступ к элементам ассоциативного массива, аналогично stdClass.
 *
 * В отличие от stdClass может инкапсулировать в себе только массив, а не скалярные значения.
 *
 */
final class ObjectAccess
{

    private array $array;


    /**
     * Конструктор класса
     *
     * @param mixed $array ассоциативный массив.
     * Принимается mixed тип, чтобы выбросить собственное исключение
     * @param bool $onlyFilled
     * @throws InvalidArgumentException
     */
    public function __construct(mixed $array, bool $onlyFilled = true)
    {
        if (!is_array($array)) {
            $type = gettype($array);
            throw new InvalidArgumentException("Переменная имеет тип: '{$type}', в то время как должно быть array");
        }
        if (!arr_is_assoc($array)) {
            throw new InvalidArgumentException('Массив должен быть ассоциативным');
        }
        if ($onlyFilled && empty($array)) {
            throw new InvalidArgumentException('Массив не должен быть пустым');
        }
        $this->array = $array;
    }



    /**
     * Возвращает элемент массива по его ключу
     *
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __get(string $key): mixed
    {
        if (!array_key_exists($key, $this->array)) {
            throw new InvalidArgumentException("В массиве отсутствует элемент по ключу: '{$key}'");
        }
        return $this->array[$key];
    }


    /**
     * Предназначен для корректных проверок на существование при использовании __get
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return array_key_exists($key, $this->array);
    }
}