<?php

namespace App\ApiServices\FileUploading\Expertise;

use App\ApiServices\FileUploading\FileUploader;
use App\Lib\Filesystem\StorageParameters;
use App\Models\Docs\DocApplication;


/**
 * Загрузчик файлов в контексте процесса экспертизы
 *
 */
abstract class ExpertiseFileUploader extends FileUploader
{

    private DocApplication $application;


    /**
     * Конструктор класса
     *
     * @param array $parameters
     * @param int $targetDocumentId
     * @param string $targetDocumentType
     */
    public function __construct(
        array $parameters,
        int $targetDocumentId,
        string $targetDocumentType
    ) {
        parent::__construct($parameters, $targetDocumentId, $targetDocumentType);

        $applicationId = $targetDocumentId;

        /** @var DocApplication $application */
        $application = DocApplication::find($applicationId); //todo тут выбирать только определенные столбцы

        $this->application = $application;
    }


    /**
     * Реализация абстрактного метода
     *
     * @return StorageParameters
     */
    public function getStorageParameters(): StorageParameters
    {
        return new StorageParameters(
            'expertise',
            "{$this->application->created_at->year}/{$this->application->id}"
        );
    }
}
