<?php

declare(strict_types=1);

namespace App\Lib\Navigation\Managers;

use App\Exceptions\Navigation\BlocksManagerException;
use App\Lib\Navigation\ValueObjects\Block;


final class DefaultBlocksManager extends BlocksManager
{

    /**
     * Реализация абстрактного метода
     *
     * @param Block[] $blocks
     * @return Block[]
     * @throws BlocksManagerException
     */
    protected function getHandledBlocks(array $blocks): array
    {
        $hasDefaultView = false;

        foreach ($blocks as $block) {

            foreach ($block->getViews() as $view) {

                $defaultRoles = $view->getDefaultRoles();

                if (!is_null($defaultRoles)) {

                    if ($this->roles->hasIntersectWithOtherInstance($defaultRoles)) {

                        $view->setSelected();
                        $hasDefaultView = true;
                        break 2;
                    }
                }
            }
        }
        if (!$hasDefaultView) {
            throw new BlocksManagerException("В блоках навигации пользователя отсутствует блок по умолчанию для ролей: '{$this->roles}'");
        }
        return $blocks;
    }
}