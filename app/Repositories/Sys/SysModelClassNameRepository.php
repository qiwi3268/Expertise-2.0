<?php


namespace App\Repositories\Sys;

use App\Repositories\Repository;
use App\Models\Sys\SysModelClassName;
use Illuminate\Support\Collection;


/**
 * @method int getIdByClassName(string $className, bool $checkIsset = true)
 */
final class SysModelClassNameRepository extends Repository
{
    protected string $modelClassName = SysModelClassName::class;


    /**
     * Возвращает коллекцию всех записей
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->getDataBaseBuilder()->get(['id', 'class_name']);
    }
}
