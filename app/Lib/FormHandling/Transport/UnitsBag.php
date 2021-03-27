<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Transport;

use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;

use App\Lib\FormHandling\Units\FormUnit;


/**
 * Сумка юнитов формы
 *
 */
final class UnitsBag
{

    /**
     * Ассоциативный массив. Ключ - id
     *
     * @var FormUnit[] $units
     */
    private array $units = [];


    /**
     * Конструктор класса
     *
     * @param FormUnit[] $units
     */
    public function __construct(array $units = [])
    {
        foreach ($units as $unit) {
            $this->add($unit);
        }
    }


    /**
     * Добавляет юнит
     *
     * id и имена юнитов должны быть уникальными
     *
     * @param FormUnit $unit
     * @return $this
     * @throws FormInvalidArgumentException
     */
    public function add(FormUnit $unit): self
    {
        $unitName = $unit->getName();

        if ($this->has($unit)) {
            throw new FormInvalidArgumentException("Юнит с идентичным id уже добавлен. name: '{$unitName}'");
        }

        foreach ($this->units as $u) {

            if ($u->getName() == $unitName) {
                throw new FormInvalidArgumentException("Юнит с идентичным name: '{$unitName}' уже добавлен");
            }
        }
        $this->units[$unit->getId()] = $unit;
        return $this;
    }


    /**
     * Проверяет наличие юнита
     *
     * @param FormUnit $unit
     * @return bool
     */
    public function has(FormUnit $unit): bool
    {
        return isset($this->units[$unit->getId()]);
    }


    /**
     * Отсутствуют ли юниты
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->units);
    }


    /**
     * Присутствует ли хоть один заполненный юнит
     *
     * @return bool
     */
    public function hasFilled(): bool
    {
        foreach ($this->units as $unit) {

            if ($unit->isFilled()) {
                return true;
            }
        }
        return false;
    }


    /**
     * Возвращает массив имён юнитов
     *
     * @return string[]
     */
    public function getNames(): array
    {
        $result = [];

        foreach ($this->units as $unit) {
            $result[] = $unit->getName();
        }
        return $result;
    }


    /**
     * Возвращает индексный массив юнитов
     *
     * @return FormUnit[]
     */
    public function getList(): array
    {
        return arr_to_list($this->units);
    }
}