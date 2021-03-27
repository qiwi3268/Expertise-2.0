<?php

declare(strict_types=1);

namespace App\Models\Utils;

use InvalidArgumentException;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\AppModel;
use stdClass;


final class RelationHandler
{


    /**
     * Возвращает свойства pivot таблицы
     *
     * Отношение BelongsToMany
     *
     * @param AppModel $model
     * @param string $relationName
     * @return stdClass
     * @throws InvalidArgumentException
     */
    public static function getBelongsToManyProperties(AppModel $model, string $relationName): stdClass
    {
        if (!method_exists($model, $relationName)) {
            $className = $model::class;
            throw new InvalidArgumentException("Класс: '{$className}' не имеет отношение: '{$relationName}'");
        }

        $relation = $model->{$relationName}();

        if (!($relation instanceof BelongsToMany)) {
            throw new InvalidArgumentException("Отношение: '{$relationName}' не принадлежит BelongsToMany");
        }

        return (object) [
            'table'   => $relation->getTable(),
            'foreign' => $relation->getForeignPivotKeyName(),
            'related' => $relation->getRelatedPivotKeyName()
        ];
    }
}