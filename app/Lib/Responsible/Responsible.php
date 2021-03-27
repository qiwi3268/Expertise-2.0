<?php

declare(strict_types=1);

namespace App\Lib\Responsible;

use App\Models\AppModel;


/**
 * Предназначен для работы с ответственными пользователями
 *
 */
final class Responsible
{

    private array $schema;

    private AppModel $model;


    /**
     * Конструктор класса
     *
     * @param int $documentId id документа
     * @param string $documentType тип документа
     */
    public function __construct(int $documentId, string $documentType)
    {
        $this->schema = yml('responsible')[$documentType];

        $this->model = doc()->getModelByDocumentType($documentType)->find($documentId);
    }

    public function test() {
        $responsibleModel = $this->schema['type_2']['model'];
        $responsibleModel = new $responsibleModel();
        $responsibleModel->deleteByMainDocumentId(62);
    }


    /**
     * Создает "Ответственные группы заявителей"
     *
     * @param int[] $accessGroupTypes индексный массив id видов групп доступа заявителей
     * согласно таблице `applicant_access_group_types`
     */
    public function createType2(array $accessGroupTypes): void
    {
        $responsibleModel = $this->schema['type_2']['model'];

        foreach ($accessGroupTypes as $typeId) {

            (new $responsibleModel())
                ->setAttribute($responsibleModel::DOC_COLUMN, $this->model->id)
                ->setAttribute('applicant_access_group_type_id', $typeId)
                ->save();
        }

        $this->model->responsible_type = 'type_2';
        $this->model->save();
    }
}
