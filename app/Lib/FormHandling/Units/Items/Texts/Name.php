<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Texts;

use App\Lib\Singles\PatternLibrary;


/**
 * Фамилия, Имя, Отчество
 */
final class Name extends TextItem
{
    protected int $maxLength = 100;


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     */
    protected function doValidate(string $value): bool
    {
        return PatternLibrary::name($value);
    }
}