<?php


namespace App\ApiServices\FileUploading\Expertise;

use App\ApiServices\FileUploading\FileRulesLibrary;
use App\ApiServices\FileUploading\UploadedFilesStorage;


final class Type1 extends ExpertiseFileUploader
{


    /**
     * Реализация абстрактного метода
     *
     * @return array
     */
    public function getInputParametersValidationRules(): array
    {
        return [
            //'files' => 'max:2'
        ];
    }


    /**
     * Реализация абстрактного метода
     *
     * @return FileRulesLibrary
     */
    public static function getFileRulesLibrary(): FileRulesLibrary
    {
        return new FileRulesLibrary(
            FileRulesLibrary::MAX_FILE_SIZES[1],
            FileRulesLibrary::ALLOWABLE_EXTENSIONS[1],
            FileRulesLibrary::FORBIDDEN_SYMBOLS[1]
        );
    }


    /**
     * Реализация абстрактного метода
     *
     * @param UploadedFilesStorage $storage
     */
    public function processStorage(UploadedFilesStorage $storage): void
    {
        foreach ($storage->getStorage() as $fileModel) {

            //...
            // $storage->addProperty('test', 'test value');
        }
    }
}
