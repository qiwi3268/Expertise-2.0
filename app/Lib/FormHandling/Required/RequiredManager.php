<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Required;

use App\Exceptions\Lib\FormHandling\RequiredManagerException;

use App\Lib\FormHandling\Units\FormUnit;
use App\Lib\FormHandling\Units\Files\FormFileSlot;
use App\Lib\FormHandling\Transport\UnitsBag;
use App\Lib\FormHandling\Display\DisplayBlock;


/*
 * Менеджер обязательных юнитов
 *
 */
final class RequiredManager
{

    private UnitsBag $unitsBag;


    /**
     * Закрытый конструктор класса
     *
     */
    private function __construct()
    {
        $this->unitsBag = new UnitsBag;
    }


    /**
     * Статический конструктор класса
     *
     * @return self
     */
    public static function create(): self
    {
        return new self;
    }


    /**
     * Статический конструктор класса
     *
     * Импортирует юниты из полученных блоков отображения
     *
     * @param DisplayBlock[] $displayBlocks блоки отображения, в которых находятся проверяемые юниты
     * @return self
     * @throws RequiredManagerException
     */
    public static function createAndImportFormUnits(array $displayBlocks = []): self
    {
        $obj = new self;

        foreach ($displayBlocks as $block) {

            if (!$block->wasProcessed()) {

                throw new RequiredManagerException("Блок отображения: '{$block->getName()}' не был обработан менеджером отображения");
            }
            $obj->addFormUnits($block->getFormUnitsList());
        }
        return $obj;
    }


    /**
     * Добавляет юниты
     *
     * @param FormUnit[] $units
     * @return $this
     */
    public function addFormUnits(array $units): self
    {
        foreach ($units as $unit) {
            $this->unitsBag->add($unit);
        }
        return $this;
    }


    /**
     * Обрабатывает юниты с ошибками
     *
     * Метод подразумевает, что никаких ошибок в юнитах быть не должно
     *
     * @throws RequiredManagerException
     */
    public function handleUnitsBagWithErrors(): void
    {
        [$err, $names] = info_implode($this->getUnitsBagWithErrors()->getNames());

        if ($err) {
            throw new RequiredManagerException("В форме присутствуют незаполненные обязательные элементы: '{$names}'");
        }
    }


    /**
     * Возвращает сумку юнитов с ошибками
     *
     * Под ошибками понимается:
     * - отображаемый слот с загруженными файлами, имеющими ошибки при проверке ЭЦП
     * - отображаемый, обязательный слот без загруженных файлов
     * - отображаемый, обязательный, незаполненный юнит
     *
     * @return UnitsBag
     * @throws RequiredManagerException
     */
    public function getUnitsBagWithErrors(): UnitsBag
    {
        if ($this->unitsBag->isEmpty()) {
            throw new RequiredManagerException('Метод не может быть вызван при отсутствующих юнитах');
        }

        $bag = new UnitsBag;

        foreach ($this->unitsBag->getList() as $unit) {

            if (
                $unit instanceof FormFileSlot
                && $unit->isVisible()
                && $unit->isFilled()
                && $unit->hasSignatureValidationError()
            ) {
                $bag->add($unit);
                continue;
            }

            if (
                $unit->isRequired()
                && $unit->isVisible()
                && !$unit->isFilled()
            ) {
                $bag->add($unit);
            }
        }
        return $bag;
    }
}