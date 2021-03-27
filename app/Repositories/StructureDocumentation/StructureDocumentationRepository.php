<?php

declare(strict_types=1);

namespace App\Repositories\StructureDocumentation;

use Illuminate\Support\Collection;
use App\Repositories\Repository;


/**
 * Корневой класс репозиториев структуры документации
 *
 * @method bool existsById(int $id)
 */
abstract class StructureDocumentationRepository extends Repository
{

    /**
     * Возвращает коллекцию структуры документации
     *
     * @return Collection
     */
    public function getAllWhereActive(): Collection
    {
        return $this->m()->where('is_active', true)
            ->orderBy('sort')
            ->toBase()
            ->get();
    }
}
