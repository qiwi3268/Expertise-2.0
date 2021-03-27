<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use LogicException;
use Illuminate\Support\Collection;


final class NodeStructure
{


    /**
     * Вычисляет глубину вложенности элементов
     *
     * Каждому объекту коллекции будет записано свойство propertyName
     *
     * @param Collection $collection
     * @param string $propertyName
     * @throws LogicException
     */
    public static function calculateDepthStructure(Collection $collection, string $propertyName = 'depth'): void
    {
        foreach ($collection as $node) {

            $depth = 0;  // Уровень вложенности узла
            $parentNodeId = $node->parent_node_id;

            if (!is_null($parentNodeId)) {

                $depth++;
                $parentIsset = true;

                // Нахождение количества родительских узлов
                do {

                    $parent = $collection->first(fn ($localNode) => ($localNode->id == $parentNodeId))
                        ?? throw new LogicException("У узла id: {$node->id} отсутствует родительский узел id: {$parentNodeId}");

                    $parentNodeId = $parent->parent_node_id;

                    if (!is_null($parentNodeId)) {
                        // Родительский узел имеет родительский узел
                        $depth++;
                    } else {
                        $parentIsset = false;
                    }
                } while ($parentIsset);
            }
            $node->{$propertyName} = $depth;
        }
    }
}
