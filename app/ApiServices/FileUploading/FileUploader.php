<?php

declare(strict_types=1);

namespace App\ApiServices\FileUploading;

use App\Lib\Filesystem\StorageParameters;

use App\Rules\Files\FileMaxSizeRule;
use App\Rules\Files\FileAllowableExtensionsRule;
use App\Rules\Files\FileForbiddenSymbolsRule;


/**
 * Общий загрузчик файлов, не относящийся к конкретному процессу, типу документа и пр.
 *
 */
abstract class FileUploader
{

    /**
     * Конструктор класса
     *
     * @param array $parameters входные параметры запроса
     * @param int $targetDocumentId целевой id документа
     * @param string $targetDocumentType целевой тип документа
     */
    public function __construct(
        protected array $parameters,
        protected int $targetDocumentId,
        protected string $targetDocumentType
    ) {}


    /**
     * Возвращает правила валидации загруженных файлов
     *
     * @return array
     */
    public function getFilesValidationRules(): array
    {
        $result = [];
        $lib = static::getFileRulesLibrary();

        if (!is_null($a = $lib->getMaxFileSize()))         $result[] = new FileMaxSizeRule($a);
        if (!is_null($a = $lib->getAllowableExtensions())) $result[] = new FileAllowableExtensionsRule($a);
        if (!is_null($a = $lib->getForbiddenSymbols()))    $result[] = new FileForbiddenSymbolsRule($a);

        return $result;
    }


    /**
     * Возвращает файловое хранилище загрузчика
     *
     * @return StorageParameters
     */
    abstract public function getStorageParameters(): StorageParameters;


    /**
     * Возвращает правила валидации входных параметров
     *
     * Возвращает полный ассоциативный массив правил.
     * В него входит проверка обязательных значей из js на предмет наличия и соответствия с БД
     *
     * @return array
     */
    abstract public function getInputParametersValidationRules(): array;


    /**
     * Возвращает библиотеку правил валидации загруженных файлов
     *
     * @return FileRulesLibrary
     */
    abstract public static function getFileRulesLibrary(): FileRulesLibrary;


    /**
     * Обрабатывает хранилище загруженных файлов
     *
     * @param UploadedFilesStorage $storage
     */
    abstract public function processStorage(UploadedFilesStorage $storage): void;
}
