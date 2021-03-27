<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items;

use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;
use App\Exceptions\Lib\FormHandling\InvalidFormUnitException;

use App\Lib\FormHandling\Units\FormUnit;


/**
 * Айтем формы
 *
 */
abstract class FormItem extends FormUnit
{

    private ?string $value;


    /**
     * Конструктор класса
     *
     * @param string|null $value
     * @param string $name
     * @param bool $required
     * @throws FormInvalidArgumentException
     * @throws InvalidFormUnitException
     */
    public function __construct(
        ?string $value,
        string $name,
        bool $required = true
    ) {
        parent::__construct($name, $required);

        if (($value = trim($value, ' ')) === '') {
            $value = null;
        }
        $valueIsset = !is_null($value);

        if ($valueIsset && !$this->validate($value)) {

            $class = class_basename($this);
            throw new InvalidFormUnitException("Айтем: '{$name}' со значением: '{$value}' не прошел валидацию класса: '{$class}'");
        }
        $this->filled = $valueIsset;
        $this->value  = $this->valueMutator($value);
    }


    /**
     * Преобразует входное значение
     *
     * Метод создан для переопределения в дочерних классах
     *
     * @param string|null $value
     * @return string|null
     */
    public function valueMutator(?string $value): ?string
    {
        return $value;
    }


    /**
     * Возвращает входное значение
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }


    /**
     * Вызывает первую callback функцию, если айтем заполнен.
     * Вызывает вторую (опциональную) callback функцию, если айтем незаполнен.
     *
     * @param callable $callback
     * @param callable|null $default
     * @return mixed результат функции
     */
    public function whenFilled(callable $callback, ?callable $default = null): mixed
    {
        if ($this->isFilled()) {
            return $callback($this);
        } elseif (!is_null($default)) {
            return $default($this);
        }
        return null;
    }


    /**
     * Валидирует полученные данные
     *
     * @param string $value
     * @return bool
     */
    abstract protected function validate(string $value): bool;
}