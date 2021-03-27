<?php


namespace App\Repositories\Miscs;

use App\Repositories\Repository;
use App\Models\Miscs\MiscRegionCode;


/**
 * @method int getIdByCode(string $code, bool $checkIsset = true)
 */
final class MiscRegionCodeRepository extends Repository
{
    protected string $modelClassName = MiscRegionCode::class;
}
