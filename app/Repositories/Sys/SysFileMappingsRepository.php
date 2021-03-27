<?php

declare(strict_types=1);

namespace App\Repositories\Sys;

use App\Exceptions\Repositories\ResultDoesNotExistException;

use App\Repositories\Repository;
use App\Models\Sys\SysFileMappings;


final class SysFileMappingsRepository extends Repository
{
    protected string $modelClassName = SysFileMappings::class;


    /**
     * Возвращает id маппингов
     *
     * @param string $snakeMappings маппинги в snake нотации
     * @return int
     * @throws ResultDoesNotExistException
     */
    public function getIdBySnakeMappings(string $snakeMappings): int
    {
        [$m1, $m2, $m3] = explode('_', $snakeMappings);

        return $this->getId([
            'level_1' => $m1,
            'level_2' => $m2,
            'level_3' => $m3
        ]);
    }
}
