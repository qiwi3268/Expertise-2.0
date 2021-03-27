<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Transport;

use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;

use App\Lib\FormHandling\Units\FormUnit;
use App\Lib\FormHandling\Units\Items\FormItem;
use App\Models\AppModel;



/**
 * Массив формы
 *
 * Вспомогательное хранилище юнитов для транспортировки и заполнения моделей БД
 *
 * Не гарантирует уникальность хранимых юнитов
 */
final class UnitsAssoc
{

    /**
     * Ассоциативный массив. Ключ - алиас
     *
     * @var FormUnit[] $units
     */
    private array $units = [];


    /**
     * Конструктор класса
     *
     * @param array $assoc
     * 1 вариант: ключ - алиас юнита. значение - юнит
     * 2 вариант: ключ - х.           значение - ассоциативный массив
     * 3 вариант: ключ - х.           значение - другой экземпляр
     * @param bool $mergeWithOtherInstance относится только к 3 варианту.
     * Требуется ли добавлять юниты другого экземпляра. В противном случае юниты будут пропущены
     */
    public function __construct(array $assoc, bool $mergeWithOtherInstance = false)
    {
        foreach ($assoc as $alias => $element) {

            if (is_array($element)) {
                $this->mergeWithArray($element);
            } elseif (($element instanceof self)) {
                if ($mergeWithOtherInstance) $this->mergeWithOtherInstance($element);
            } else {
                $this->add($alias, $element);
            }
        }
    }


    /**
     * Добавляет юнит
     *
     * @param string $alias
     * @param FormUnit $unit
     * @return $this
     * @throws FormInvalidArgumentException
     */
    public function add(string $alias, FormUnit $unit): self
    {
        if (is_numeric($alias)) {
            throw new FormInvalidArgumentException("Алиас юнита: '{$alias}' не может быть числовым значением");
        }
        $this->units[$this->checked($alias, false)] = $unit;
        return $this;
    }


    /**
     * Возвращает юнит
     *
     * @param string $alias
     * @return FormUnit
     */
    public function __get(string $alias): FormUnit
    {
        return $this->units[$this->checked($alias)];
    }


    /**
     * Возвращает проверенный алиас
     *
     * @param string $alias
     * @param bool $shouldIsset
     * @return string
     * @throws FormInvalidArgumentException
     */
    private function checked(string $alias, bool $shouldIsset = true): string
    {
        $isset = isset($this->units[$alias]);

        if ($shouldIsset && !$isset) {
            throw new FormInvalidArgumentException("Айтем с alias: '{$alias}' не добавлен");
        }
        if (!$shouldIsset && $isset) {
            throw new FormInvalidArgumentException("Айтем с alias: '{$alias}' уже добавлен");
        }
        return $alias;
    }


    /**
     * Выполняет инъекцию значений айтемов в модель
     *
     * @param AppModel $model
     * @return AppModel
     */
    public function injectIntoModel(AppModel $model): AppModel
    {
        return $model->injectionFromUnitsAssocs([$this]);
    }


    /**
     * Создает сумку юнитов
     *
     * @return UnitsBag
     */
    public function createUnitsBag(): UnitsBag
    {
        return new UnitsBag($this->units);
    }


    /**
     * Возвращает ассоциативный массив значений айтемов
     *
     * @return array
     */
    public function getFormItemsAssoc(): array
    {
        $result = [];

        foreach ($this->units as $alias => $unit) {

            if ($unit instanceof FormItem) {

                $result[$alias] = $unit->getValue();
            }
        }
        return $result;
    }


    /**
     * Добавляет в текущий экземпляр все юниты другого из экземпляра
     *
     * @param UnitsAssoc $unitsAssoc
     * @return $this
     */
    public function mergeWithOtherInstance(self $unitsAssoc): self
    {
        // Обращение к приватному свойству другого экземпляра
        return $this->mergeWithArray($unitsAssoc->units);
    }


    /**
     * Добавляет в текущий экземпляр все юниты из ассоциативного массива
     *
     * @param FormUnit[] $assoc
     * @return $this
     */
    public function mergeWithArray(array $assoc): self
    {
        foreach ($assoc as $alias => $unit) {

            $this->add($alias, $unit);
        }
        return $this;
    }
}