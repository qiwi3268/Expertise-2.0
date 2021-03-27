<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Texts;

use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;
use App\Exceptions\Lib\FormHandling\InvalidFormUnitException;

use App\Lib\FormHandling\Units\Items\FormItem;


/**
 * Айтем, характеризующий текстовое поле
 *
 */
abstract class TextItem extends FormItem
{

    /**
     * @var int максимальная длина, согласно полю в БД
     */
    protected int $maxLength;


    /**
     * Конструктор класса
     *
     * @param string|null $value
     * @param string $name
     * @param bool $required
     * @throws FormLogicException
     * @throws InvalidFormUnitException
     */
    public function __construct(
        ?string $value,
        string $name,
        bool $required = true
    ) {
        if (!isset($this->maxLength)) {
            throw new FormLogicException("Свойство 'maxLength' не инициализировано");
        }
        if ($this->maxLength <= 0) {
            throw new FormInvalidArgumentException('Длина текстового поля должна быть больше 0 символов');
        }
        parent::__construct($value, $name, $required);
    }


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     */
    protected function validate(string $value): bool
    {
        return (mb_strlen($value) <= $this->maxLength) && $this->doValidate($value);
    }


    /**
     * Валидирует текстовые данные, не включая проверку на длину строки
     *
     * @param string $value
     * @return bool
     */
    abstract protected function doValidate(string $value): bool;
}