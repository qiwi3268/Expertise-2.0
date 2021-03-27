<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Texts;

use App\Lib\Singles\PatternLibrary;


final class Kpp extends TextItem
{
    protected int $maxLength = 9;


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     */
    protected function doValidate(string $value): bool
    {
        return PatternLibrary::kpp($value);
    }
}