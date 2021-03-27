<?php

declare(strict_types=1);

namespace App\Providers\Ais;

use RuntimeException;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Repositories\Sys\SysModelClassNameRepository;


/**
 * Смысл провайдера заключается в том, что мы используем полиморфные отношения eloquent,
 * и при этом на уровне БД тип класса реализован как внешний ключ к таблице sys_model_class_names.
 *
 * Таким образом, полные названия классов хранятся в одном месте, ускоряется взаимодействие с БД.
 *
 */
final class ModelClassNameRelationServiceProvider extends ServiceProvider
{

    /**
     *
     * @return void
     */
    public function register(): void
    {
    }


    /**
     * Предназначен для загрузки отношений полных названий классов модели
     *
     * @return void
     * @throws RuntimeException
     */
    public function boot(): void
    {
        $map = [];

        foreach ((new SysModelClassNameRepository)->getAll() as $model) {

            $className = $model->class_name;

            if (!class_exists($className)) {
                throw new RuntimeException("Класс модели: '{$className}' не существует");
            }
            $map[$model->id] = $className;
        }

        // Ключ соответствует id из таблицы sys_model_class_names
        Relation::morphMap($map);
    }
}
