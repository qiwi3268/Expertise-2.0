<?php

declare(strict_types=1);

namespace App\Repositories\Files;

use App\Repositories\Repository;
use App\Models\Files\File;


/**
 * @method int getIdByHashName(string $hashName, bool $checkIsset = true)
 * @method int getIdAndFileSizeByHashName(string $hashName, bool $checkIsset = true)
 * @method string getOriginalNameByHashName(string $hashName, bool $checkIsset = true)
 * @method bool existsBySubDirectoryAndHashName(string $subDirectory, string $hashName)
 * @method bool existsBySubDirectoryAndHashNameAndIsNeeds(string $subDirectory, string $hashName, bool $isNeeds)
 */
final class FileRepository extends Repository
{
    protected string $modelClassName = File::class;


    /**
     * Возвращает модель по хэш имени файла
     *
     * @param string $hashName
     * @param array $columns
     * @return File|null
     */
    public function getByHashName(string $hashName, array $columns = ['*']): ?File
    {
        /** @var File|null $result */
        $result = $this->m()
            ->select($columns)
            ->where('hash_name', $hashName)
            ->first();
        return $result;
    }
}
