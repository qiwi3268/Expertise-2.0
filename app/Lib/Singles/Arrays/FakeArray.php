<?php

declare(strict_types=1);

namespace App\Lib\Singles\Arrays;

use InvalidArgumentException;
use ArrayAccess;


/**
 * Представляет собой фэйковый массив
 *
 * При обращении к любому ключу будет возвращать null.
 * Имеется возможность в установлении исключительных значений,
 * при обращении к которым будут возвращены нужные данные
 */
final class FakeArray implements ArrayAccess
{
    private array $exclusion = [];


    /**
     * Конструктор класса
     *
     * @param array $exclusions
     */
    public function __construct(array $exclusions = [])
    {
        foreach ($exclusions as $offset => $value) {

            $this->setExclusion($offset, $value);
        }
    }


    /**
     * Устанавливает исключительные данные
     *
     * @param string|int $offset
     * @param mixed $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setExclusion(string|int $offset, mixed $value): self
    {
        if (array_key_exists($offset, $this->exclusion)) {

            throw new InvalidArgumentException("Исключительные данные по ключу: '{$offset}' уже установлены");
        }
        if (is_null($value)) { // null не принимается, т.к. он и так будет возвращён по умолчанию

            throw new InvalidArgumentException("Исключительные данные по ключу: '{$offset}' не могут быть типом null");
        }
        $this->exclusion[$offset] = $value;
        return $this;
    }


    /**
     * Возвращает null или исключительное значение, если оно установлено
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->exclusion[$this->checkedOffset($offset)] ?? null;
    }


    /**
     * Возвращает проверенное значение смещения
     *
     * @param mixed $offset
     * @return string|int
     * @throws InvalidArgumentException
     */
    private function checkedOffset(mixed $offset): string|int
    {
        if (!is_string($offset) && !is_int($offset)) {
            $type = gettype($offset);
            throw new InvalidArgumentException("Значение смещения имеет тип: '{$type}', в то время как должно быть string или int");
        }
        return $offset;
    }


    /**
     * Заглушка для реализации интерфейса
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {return true;}

    /**
     * Заглушка для реализации интерфейса
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {}

    /**
     * Заглушка для реализации интерфейса
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void
    {}
}