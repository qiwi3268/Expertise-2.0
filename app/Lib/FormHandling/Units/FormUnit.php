<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units;

use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;
use App\Exceptions\Lib\FormHandling\FormLogicException;

use App\Lib\Traits\Identifiable;


/**
 * Юнит формы
 *
 * Представляет собой основополагающее звено, которое можно охарактеризовать:
 * - заполняемостью
 * - обязательностью
 * - видимостью
 */
abstract class FormUnit
{
    use Identifiable;

    /**
     * Заполнен ли юнит
     *
     */
    protected bool $filled;

    /**
     * Отображается ли юнит на странице
     *
     * Флаг отображения на странице имеет значение по умолчанию, т.к.
     * юнит может не быть обработан менеджером отображения
     */
    protected bool $visible = true;


    /**
     * Конструктор класса
     *
     * @param string $name имя (описание) юнита
     * @param bool $required Обязательный ли юнит
     * @throws FormInvalidArgumentException
     */
    public function __construct(protected string $name, protected bool $required = true)
    {
        if (empty($name)) {
            throw new FormInvalidArgumentException('Имя юнита не может быть пустым');
        }
        $this->generateId();
    }


    /**
     * Возвращает имя юнита
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Заполнен ли юнит
     *
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->filled;
    }


    /**
     * Устанавливает флаг обязательности юнита
     *
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }


    /**
     * Обязательный ли юнит
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }


    /**
     * Устанавливает флаг отображения юнита на странице
     *
     * @param bool $visible
     * @throws FormLogicException
     */
    public function setVisible(bool $visible): void
    {
        if ($this->isFilled() && !$visible) {
            throw new FormLogicException("Попытка установить заполненному юниту: '{$this->name}' отрицательный флаг отображения на странице");
        }
        $this->visible = $visible;
    }


    /**
     * Отображается ли юнит на странице
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }
}