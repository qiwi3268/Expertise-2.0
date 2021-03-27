<?php

declare(strict_types=1);

namespace App\Lib\Navigation\Managers;

use App\Exceptions\Navigation\NavigationException;

use App\Lib\Settings\NavigationManager;
use App\Lib\Navigation\ValueObjects\Block;
use App\Lib\Singles\Roles;



/**
 * Менеджер блоков навигации
 *
 */
abstract class BlocksManager
{

    private NavigationManager $manager;

    /**
     * @var Block[] класса
     */
    private array $blocks;


    /**
     * Конструктор класса
     *
     * @param Roles $roles
     * @throws NavigationException
     */
    public function __construct(protected Roles $roles)
    {
        $this->manager = NavigationManager::getInstance();

        $blocks = $this->getBlocksForRoles($roles);

        if (empty($blocks)) {

            throw new NavigationException("Для ролей пользователя: '{$roles}' не найдено блоков навигации");
        }
        $this->blocks = $this->getHandledBlocks($blocks);
    }


    /**
     * Возвращает блоки навигации для ролей
     *
     * @param Roles $roles
     * @return Block[]
     */
    private function getBlocksForRoles(Roles $roles): array
    {
        $result = [];

        foreach ($this->manager->getSchema() as $blockSchema) {

            $block = new Block($blockSchema);

            if ($roles->hasIntersectWithOtherInstance($block->getRoles())) {

                $result[] = $block;
            }
        }
        return $result;
    }


    /**
     * Возвращает массив блоков навигации
     *
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }


    /**
     * Возвращает обработанные блоки навигации
     *
     * В обработку входят:
     * - Проверка на логические ошибки
     * - Установка свойства $selected у объектов View
     *
     * @param Block[] $blocks
     * @return Block[]
     */
    abstract protected function getHandledBlocks(array $blocks): array;
}