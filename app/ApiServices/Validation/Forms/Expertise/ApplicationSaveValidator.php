<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Forms\Expertise;

use App\ApiServices\Validation\StepValidator;
use App\Models\Docs\DocApplication;


final class ApplicationSaveValidator extends StepValidator
{

    /**
     * Валидация общих входных параметров
     *
     * Логирует ошибки
     *
     */
    public function commonInputParametersValidation(): void
    {
        $model = DocApplication::class;

        $this->validateClientInput($this->req->all(), [
            'applicationId' => ['bail', 'required', 'numeric', "exists:{$model},id"]
        ]);
    }
}
