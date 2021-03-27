<?php

declare(strict_types=1);

namespace App\Models\Files;

use App\Models\AppModel;
use App\Lib\Csp\Validation\ValueObjects\Public\Signer;
use App\Lib\Csp\Validation\ValueObjects\Public\ValidationResult;


/**
 * Файлы системы
 *
 */
final class File extends AppModel
{

    protected $fillable = [
        'doc_id',
        'doc_type',
        'user_id',
        'original_name',
        'file_size',
        'sys_filesystem_disk_id',
        'sub_directory',
        'sys_file_mappings_id',
        'hash_name'
    ];

    protected $casts = [
        'is_needs'     => 'bool',
        'cron_mark_at' => 'datetime',
    ];

    private ValidationResult $validationResult;


    /**
     * Конструктор класса
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->validationResult = new ValidationResult;
    }


    /**
     * Добавляет подписанта к текущему файлу
     *
     * @param Signer $signer
     */
    public function addSigner(Signer $signer): void
    {
        $this->validationResult->addSigner($signer);
    }


    /**
     * Определяет был ли подписан файл открепленной или встроенной подписью
     *
     * Метод необходимо вызывать после того, как текущий экземпляр пройдет
     * через {@see \App\Lib\Files\SignedFiles::calculate()}
     * @return bool
     */
    public function hasSigners(): bool
    {
        return $this->validationResult->hasSigners();
    }


    /**
     * Возвращает массив подписантов
     *
     * @return Signer[]
     */
    public function getSigners(): array
    {
        return $this->validationResult->getSigners();
    }
}
