<?php

declare(strict_types=1);

namespace App\Repositories\Miscs;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Repository;
use App\Models\Miscs\MiscModel;


/**
 * Универсальный репозиторий справочников
 *
 */
final class MiscRepository extends Repository
{

    protected string $modelClassName;


    /**
     * Конструктор класса
     *
     * @param string|MiscModel $model
     */
    public function __construct(string|MiscModel $model)
    {
        $this->modelClassName = ($model instanceof MiscModel) ? $model::class : $model;

        parent::__construct();
    }


    /**
     * Возвращает модель по id записи
     *
     * @param int $id
     * @return MiscModel|null
     */
    public function getById(int $id): ?MiscModel
    {
        /** @var MiscModel|null $result */
        $result = $this->m()
            ->select(['id', 'name', 'is_active'])
            ->where('id', $id)
            ->first();
        return $result;
    }


    /**
     * Возвращает коллекцию всех активных записей
     *
     * @return Collection
     */
    public function getAllWhereActive(): Collection
    {
        return $this->m()->where('is_active', true)
            ->orderBy('sort')
            ->get();
    }


    /**
     * Возвращает коллекцию зависимых активных справочников по id главного справочника
     *
     * @param int $id
     * @param string $relationName
     * @return Collection
     */
    public function getRelatedWhereActiveById(int $id, string $relationName): Collection
    {
        $model = $this->m()->where('id', $id)
            ->with([$relationName => function (BelongsToMany $query) {
                $query->where('is_active', true)
                    ->orderBy('sort');
            }])->first(['id']);

        return is_null($model) ? new Collection : $model->{$relationName};
    }
}
