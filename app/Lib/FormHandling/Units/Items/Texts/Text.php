<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Texts;

use App\Exceptions\Lib\FormHandling\InvalidFormUnitException;


final class Text extends TextItem
{

    /**
     * Конструктор класса
     *
     * @param string|null $value
     * @param string $name
     * @param int $maxLength
     * @param bool $required
     * @throws InvalidFormUnitException
     */
    public function __construct(
        ?string $value,
        string $name,
        protected int $maxLength,
        bool $required = true
    ) {
        parent::__construct($value, $name, $required);
    }


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     */
    protected function doValidate(string $value): bool
    {
        return true;
    }
}