<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Filesystem\StarPathHandler;


/**
 * Правила валидации starPath
 *
 */
final class StarPathRule implements Rule
{

    /**
     * Конструктор класса
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param string $starPath
     * @return bool
     */
    public function passes($attribute, $starPath): bool
    {
        return is_string($starPath) && StarPathHandler::stringValidate($starPath);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return 'Полученная строка starPath некорректна';
    }
}
