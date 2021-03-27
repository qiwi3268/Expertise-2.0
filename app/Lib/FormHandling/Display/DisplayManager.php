<?php


namespace App\Lib\FormHandling\Display;

use App\Exceptions\Lib\FormHandling\DisplayManagerException;
use App\Exceptions\Lib\FormHandling\DisplayBlockException;

use App\Lib\Singles\ComparisonRule;
use App\Lib\FormHandling\Units\Items\FormItem;


/**
 * Менеджер блоков отображения формы
 *
 */
final class DisplayManager
{

    /**
     * @var DisplayBlock[] $blocks
     */
    private array $displayBlocks = [];

    /**
     * @var ComparisonRule[] $rules
     */
    private array $rules = [];

    private int $arrayIndex = 0;

    /**
     * Была ли уже выполнена обработка
     */
    private bool $processPassed = false;


    /**
     * Закрытый конструктор класса
     *
     * @param FormItem $mainItem
     */
    private function __construct(private FormItem $mainItem)
    {}


    /**
     * Статический конструктор класса
     *
     * @param FormItem $mainItem айтем, от значения которого зависит выбор блока отображения
     * @return self
     */
    public static function create(FormItem $mainItem): self
    {
       return new self($mainItem);
    }


    /**
     * Добавляет правило и соответствующий ему блок отображения
     *
     * @param ComparisonRule $rule
     * @param DisplayBlock $block
     * @return $this
     * @throws DisplayManagerException
     */
    public function add(ComparisonRule $rule, DisplayBlock $block): self
    {
        if ($block->wasProcessed()) {
            throw new DisplayManagerException("Блок отображения: '{$block->getName()}' был обработан ранее");
        }

        $this->rules[$this->arrayIndex] = $rule;
        $this->displayBlocks[$this->arrayIndex++] = $block;
        return $this;
    }


    /**
     * Добавляет последнее правило, блок отображения и выполненяет их обработку
     *
     * @param ComparisonRule $rule
     * @param DisplayBlock $block
     * @param bool $shouldVisible
     * @return self
     * @throws DisplayBlockException
     * @throws DisplayManagerException
     */
    public function addAndProcess(ComparisonRule $rule, DisplayBlock $block, bool $shouldVisible = false): self
    {
        return $this->add($rule, $block)->process($shouldVisible);
    }


    /**
     * Возвращает отображаемый на странице блок
     *
     * @param bool $shouldVisible должен ли присутствовать отображаемый на странице блок
     * @return DisplayBlock|null
     * @throws DisplayBlockException
     * @throws DisplayManagerException
     */
    public function getVisibleDisplayBlock(bool $shouldVisible = true): ?DisplayBlock
    {
        if (!$this->processPassed) {
            throw new DisplayManagerException('Не был вызван метод обработки');
        }
        foreach ($this->displayBlocks as $block) {

            if ($block->isVisible()) {

                return $block; // Отображемый блок может быть только один
            }
        }
        if ($shouldVisible) {
            throw new DisplayManagerException('Отсутствует обязательный блок отображения');
        }
        return null;
    }


    /**
     * Выполняет обработку
     *
     * Метод должен вызываться только один раз, т.к. изменяется состояние блоков отображения
     *
     * @param bool $shouldVisible должен ли присутствовать отображаемый на странице блок
     * при заполненном главном значении
     * @return $this
     * @throws DisplayManagerException
     * @throws DisplayBlockException
     */
    public function process(bool $shouldVisible = false): self
    {
        if (empty($this->displayBlocks)) {
            throw new DisplayManagerException('Метод не может быть вызван при отсутствующих блоках отображения');
        }
        if ($this->processPassed) {
            throw new DisplayManagerException('Метод обработки может быть вызван единожды');
        }
        $this->processPassed = true;

        // Главное значение заполнено
        if ($this->mainItem->isFilled()) {

            $matchingNames = []; // Подошедшие под правило имена блоков

            for ($f = 0; $f < count($this->rules); $f++) {

                $rule = $this->rules[$f];
                $block = $this->displayBlocks[$f];

                $block->markProcessed();

                if ($rule->compare($this->mainItem->getValue())) {

                    $matchingNames[] = $block->getName();

                    if (count($matchingNames) > 1) {

                        $debug = implode(' и ', $matchingNames);
                        throw new DisplayManagerException("Под правило подходит несколько блоков отображения: '{$debug}'");
                    }
                    $block->setVisible(true);
                } elseif ($block->isFilled()) {

                    throw new DisplayManagerException("Блок отображения: '{$block->getName()}' заполнен при отрицательном результате сравнения");
                } else {

                    $block->setVisible(false);
                }
            }
            if ($shouldVisible && count($matchingNames) == 0) {
                throw new DisplayManagerException("При заполненном айтеме: '{$this->mainItem->getName()}' отсутствует отображаемый на странице блок формы");
            }
        } else {

            foreach ($this->displayBlocks as $block) {

                $block->markProcessed();

                if ($block->isFilled()) {
                    throw new DisplayManagerException("Блок отображения: '{$block->getName()}' заполнен при незаполненном главном значении");
                }
                $block->setVisible(false);
            }
        }
        return $this;
    }
}