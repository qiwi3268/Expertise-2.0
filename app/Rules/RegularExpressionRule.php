<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;


/**
 * Правила валидации регулярным выражением
 *
 */
final class RegularExpressionRule implements Rule
{

    /**
     * Конструктор класса
     *
     * @param string $name
     * @param string $pattern
     */
    public function __construct(private string $name, private string $pattern)
    {
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param string $subject
     * @return bool
     */
    public function passes($attribute, $subject): bool
    {
        return is_string($subject) && pm($this->pattern, $subject);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Элемент: '{$this->name}' не подходит под регулярное выражение: '{$this->pattern}'";
    }
}
