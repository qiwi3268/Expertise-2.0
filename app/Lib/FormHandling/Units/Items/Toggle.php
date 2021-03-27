<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items;


final class Toggle extends FormItem
{

    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     */
    protected function validate(string $value): bool
    {
        return $value == '-1' || $value == '1';
    }


    /**
     * Преобразует значение к формату БД
     *
     * @param string|null $value
     * @return string|null
     */
    public function valueMutator(?string $value): ?string
    {
        return ($value === '-1') ? '0' : $value;
    }
}