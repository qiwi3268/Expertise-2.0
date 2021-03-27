<?php

namespace App\Rules\Arrays;

use Illuminate\Contracts\Validation\Rule;


/**
 * Правила валидации индексного строкового массива
 *
 */
final class StringListArrayRule implements Rule
{


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param array $array
     * @return bool
     */
    public function passes($attribute, $array): bool
    {
        return is_array($array) && arr_is_string_list($array);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return 'Элемент не является индексным строковым массивом';
    }
}
