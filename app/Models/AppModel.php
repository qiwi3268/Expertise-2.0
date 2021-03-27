<?php

declare(strict_types=1);

namespace App\Models;

use LogicException;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use App\Lib\FormHandling\Transport\UnitsAssoc;


/**
 * Расширенный класс корневой модели
 *
 * @mixin Eloquent
 */
abstract class AppModel extends Model
{


    /**
     * Проверяет наличие требуемых аттрибутов в модели
     *
     * @param string[] $attributes
     * @param bool $throw требуется ли выбрасывать исключение, если нет всех аттрибутов
     * @return bool
     * @throws LogicException
     */
    public function existsAttributes(array $attributes, bool $throw = true): bool
    {
        $keys =  array_keys($this->getAttributes());

        [$err, $debug] = info_implode(array_diff($attributes, $keys));

        if ($err){
            return $throw ? throw new LogicException("В модели отсутствуют обязательные аттрибуты: '{$debug}'") : false;
        }
        return true;
    }


    /**
     * Выполняет в себя инъекцию значений из массива формы
     *
     * @param UnitsAssoc[] $unitsAssocs
     * @param bool $overwrite разрешено ли перезаписывать аттрибуты,
     * которые были добавлены другим массивом формы
     * @return static
     * @throws LogicException
     */
    public function injectionFromUnitsAssocs(array $unitsAssocs, bool $overwrite = false): static
    {
        $attributes = [];

        // Сделано в два цикла, чтобы контролировать перезапись аттрибутов, добавляемых из массива формы
        foreach ($unitsAssocs as $assoc) {

            foreach ($assoc->getFormItemsAssoc() as $key => $value) { // Записываются только айтемы

                if (!$overwrite && array_key_exists($key, $attributes)) {
                    throw new LogicException("Аттрибут по ключу: '{$key}' уже добавлен");
                }
                $attributes[$key] = $value;
            }
        }
        arr_each($attributes, fn ($value, $key) => $this->setAttribute($key, $value));
        return $this;
    }
}
