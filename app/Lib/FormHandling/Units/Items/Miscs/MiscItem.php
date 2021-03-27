<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Miscs;

use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;
use App\Exceptions\Lib\FormHandling\InvalidFormUnitException;

use App\Lib\FormHandling\Units\Items\FormItem;
use App\Lib\FormHandling\Units\Utils\DependentMiscRelation;
use App\Lib\Settings\Miscs\SingleMiscsManager;


/**
 * Айтем, характеризующий выбранный справочник
 *
 */
abstract class MiscItem extends FormItem
{

    protected string $miscClassName;


    /**
     * Конструктор класса
     *
     * @param string|null $value
     * @param string $name
     * @param string $alias
     * @param bool $required
     * @throws FormInvalidArgumentException
     * @throws InvalidFormUnitException
     */
    public function __construct(
        ?string $value,
        string $name,
        protected string $alias,
        bool $required = true
    ) {

        $mgr = SingleMiscsManager::getInstance();

        if (!$mgr->existsByAlias($alias)) {
            throw new FormInvalidArgumentException("Некорректный алиас справочника: '{$alias}'");
        }
        $this->miscClassName = $mgr->getClassNameByAlias($alias);

        parent::__construct($value, $name, $required);
    }


    /**
     * Возвращает алиас справочника
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }


    /**
     * Фабричный метод создания зависимого справочника с валидацией отношения
     *
     * @param string|null $subValue
     * @param string $subName
     * @param string $subAlias
     * @param SingleMisc $mainMisc
     * @param bool $required
     * @return static
     * @throws FormLogicException
     * @throws InvalidFormUnitException
     */
    public static function createSubMisc(
        ?string $subValue,
        string $subName,
        string $subAlias,
        SingleMisc $mainMisc,
        bool $required = true
    ): static {

        $subMisc = new static($subValue, $subName, $subAlias, $required);

        if ($subMisc->isFilled()) {

            $mainName = $mainMisc->getName();

            if (!$mainMisc->isFilled()) {

                throw new FormLogicException("Главный справочник: '{$mainName}' незаполнен при заполненном зависимом справочнике: '{$subName}'");
            }
            if (!DependentMiscRelation::existsRelation($mainMisc, $subMisc)) {

                throw new InvalidFormUnitException("Зависимый справочник: '{$subName}' не прошел валидацию отношения с главным справочником: '{$mainName}'");
            }
        }
        return $subMisc;
    }
}