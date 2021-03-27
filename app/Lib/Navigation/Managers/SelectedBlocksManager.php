<?php

declare(strict_types=1);

namespace App\Lib\Navigation\Managers;

use App\Exceptions\Navigation\BlocksManagerException;
use App\Exceptions\Navigation\NavigationException;

use App\Lib\Navigation\ValueObjects\Block;
use App\Lib\Singles\Roles;


final class SelectedBlocksManager extends BlocksManager
{

    /**
     * Конструктор класса
     *
     * @param Roles $roles
     * @param string $b block['name']
     * @param string $v view['name']
     * @throws NavigationException
     */
    public function __construct(
        Roles $roles,
        private string $b,
        private string $v
    ) {
        parent::__construct($roles);
    }


    /**
     * Реализация абстрактного метода
     *
     * @param Block[] $blocks
     * @return Block[]
     * @throws BlocksManagerException
     */
    protected function getHandledBlocks(array $blocks): array
    {
        $hasBlock = false;

        foreach ($blocks as $block) {

            if ($block->getName() == $this->b) {

                $hasView = false;

                foreach ($block->getViews() as $view) {

                    if ($view->getName() == $this->v) {

                        $view->setSelected();
                        $hasView = true;
                        break;
                    }
                }

                if (!$hasView) {
                    throw new BlocksManagerException("В блоке навигации пользователя: '{$this->b}' отсутствует выбранное отображение: '{$this->v}'");
                }
                $hasBlock = true;
                break;
            }
        }
        if (!$hasBlock) {
            throw new BlocksManagerException("В блоках навигации пользователя отсутствует выбранный блок: '{$this->b}'");
        }
        return $blocks;
    }
}