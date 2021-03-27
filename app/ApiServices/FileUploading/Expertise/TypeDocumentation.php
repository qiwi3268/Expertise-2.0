<?php


namespace App\ApiServices\FileUploading\Expertise;

use App\ApiServices\FileUploading\FileRulesLibrary;
use App\ApiServices\FileUploading\UploadedFilesStorage;

use App\Models\Files\FileDocumentation;
use App\Repositories\Sys\SysModelClassNameRepository;
use App\Lib\Singles\TypeOfObjectBridge;
use App\Rules\StructureDocumentationRule;


/**
 * Документация
 *
 */
final class TypeDocumentation extends ExpertiseFileUploader
{

    /**
     * Реализация абстрактного метода
     *
     * @return array
     */
    public function getInputParametersValidationRules(): array
    {
        return [
            'structureNodeId' => [
                'bail',
                'required',
                'numeric',
                new StructureDocumentationRule($this->parameters['mappings'])
            ]
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
        $bridge = TypeOfObjectBridge::createByDocumentationMappings($this->parameters['mappings']);
        $model = $bridge->getStructureDocumentationModel();
        $className = $model::class;

        $rep = new SysModelClassNameRepository;

        $structureId = $this->parameters['structureNodeId'];
        $structureType = $rep->getIdByClassName($className);

        foreach ($storage->getStorage() as $fileModel) {

            FileDocumentation::create([
                'structure_id'   => $structureId,
                'structure_type' => $structureType,
                'file_id'        => $fileModel->id
            ]);
        }
    }
}
