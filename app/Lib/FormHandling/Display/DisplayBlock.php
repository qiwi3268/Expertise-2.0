<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Display;

use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;
use App\Exceptions\Lib\FormHandling\DisplayBlockException;

use App\Lib\FormHandling\Units\FormUnit;
use App\Lib\FormHandling\Transport\UnitsAssoc;
use App\Lib\FormHandling\Transport\UnitsBag;

use App\Lib\Traits\Identifiable;


/**
 * Блок отображения формы
 *
 */
final class DisplayBlock
{
    use Identifiable;

    private UnitsBag $unitsBag;
    private bool $visible;
    private bool $processed = false;


    /**
     * Конструктор класса
     *
     * @param string $name имя (описание) блока. Используется в исключениях, как отладочная информация
     * @throws FormInvalidArgumentException
     */
    public function __construct(private string $name)
    {
        if (empty($name)) {
            throw new FormInvalidArgumentException('Имя блока отображения не может быть пустым');
        }
        $this->unitsBag = new UnitsBag;
        $this->generateId();
    }


    /**
     * Статический конструктор класса
     *
     * @param string $name
     * @param UnitsBag|UnitsAssoc $transportable
     * @return self
     */
    public static function createFromTransportable(string $name, UnitsBag|UnitsAssoc $transportable): self
    {
        $bag = $transportable instanceof UnitsAssoc
            ? $transportable->createUnitsBag()
            : $transportable;

        return (new self($name))->importFromUnitsBag($bag);
    }


    /**
     * Добавляет юнит
     *
     * @param FormUnit $unit
     * @return $this
     */
    public function addFormUnit(FormUnit $unit): self
    {
        $this->unitsBag->add($unit);
        return $this;
    }


    /**
     * Импортирует юниты из сумки
     *
     * @param UnitsBag $bag
     * @return $this
     */
    public function importFromUnitsBag(UnitsBag $bag): self
    {
        foreach ($bag->getList() as $unit) {

            $this->addFormUnit($unit);
        }
        return $this;
    }


    /**
     * Возвращает имя
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * Является ли блок тем-же объектом, что передан для проверки
     *
     * @param self $block
     * @return bool
     */
    public function is(self $block): bool
    {
        return $this->getId() == $block->getId();
    }


    /**
     * Присутствуют ли в блоке заполненные юниты
     *
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->unitsBag->hasFilled();
    }


    /**
     * Устанавливает отметку о том, что экземпляр обработан менеджером отображения
     *
     * @throws DisplayBlockException
     */
    public function markProcessed(): void
    {
        if ($this->processed) {
            throw new DisplayBlockException("Блок отображения: '{$this->getName()}' уже был обработан менеджером отображения");
        }
        $this->processed = true;
    }


    /**
     * Был ли блок обработан менеджером отображения
     *
     * @return bool
     */
    public function wasProcessed(): bool
    {
        return $this->processed;
    }


    /**
     * Устанавливает флаг отображения на странице
     *
     * @param bool $visible
     * @throws DisplayBlockException
     */
    public function setVisible(bool $visible): void
    {
        if (isset($this->visible)) {
            throw new DisplayBlockException("Флаг отображения блока: '{$this->getName()}' на странице уже установлен");
        }
        $this->visible = $visible;

        foreach ($this->getFormUnitsList() as $unit) {
            $unit->setVisible($visible);
        }
    }


    /**
     * Отображается ли блок на странице
     *
     * @return bool
     * @throws DisplayBlockException
     */
    public function isVisible(): bool
    {
        if (!isset($this->visible)) {
            throw new DisplayBlockException("Флаг отображения блока: '{$this->getName()}' на странице не установлен");
        }
        return $this->visible;
    }


    /**
     * Возвращает индексный массив юнитов
     *
     * @return FormUnit[]
     */
    public function getFormUnitsList(): array
    {
        return $this->unitsBag->getList();
    }
}