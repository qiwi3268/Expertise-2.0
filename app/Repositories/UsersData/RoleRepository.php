<?php

declare(strict_types=1);

namespace App\Repositories\UsersData;

use App\Exceptions\Repositories\ResultDoesNotExistException;

use App\Repositories\Repository;
use App\Models\UsersData\Role;
use Illuminate\Support\Collection;


final class RoleRepository extends Repository
{
    protected string $modelClassName = Role::class;


    /**
     * Возвращает коллекцию всех записей
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->getDataBaseBuilder()->get();
    }
}
