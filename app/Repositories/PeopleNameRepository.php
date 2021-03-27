<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Models\PeopleName;


final class PeopleNameRepository extends Repository
{
    protected string $modelClassName = PeopleName::class;


    /**
     * Возвращает коллекцию всех имён
     *
     * @return Collection
     */
    public function getAllNames(): Collection
    {
        return $this->getDataBaseBuilder()->select(['name'])->get();
    }
}
