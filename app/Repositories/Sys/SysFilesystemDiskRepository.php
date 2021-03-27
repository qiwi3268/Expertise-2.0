<?php


namespace App\Repositories\Sys;

use App\Repositories\Repository;
use App\Models\Sys\SysFilesystemDisk;


/**
 * @method int getIdByName(string $name, bool $checkIsset = true)
 */
final class SysFilesystemDiskRepository extends Repository
{
    protected string $modelClassName = SysFilesystemDisk::class;
}
