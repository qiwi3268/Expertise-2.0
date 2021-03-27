<?php

declare(strict_types=1);

namespace App\Repositories\Forms;

use App\Repositories\Repository;
use App\Models\Forms\FormDocApplication;


final class FormDocApplicationRepository extends Repository
{
    protected string $modelClassName = FormDocApplication::class;


    /**
     * Возвращает модель по id документа заявления
     *
     * @param int $id
     * @return FormDocApplication|null
     */
    public function getByDocApplicationId(int $id): ?FormDocApplication
    {
        /** @var FormDocApplication|null $result */
        $result =  $this->m()->where('doc_application_id', $id)->first();

        return $result;
    }

}
