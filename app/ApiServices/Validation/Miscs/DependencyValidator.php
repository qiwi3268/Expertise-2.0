<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Miscs;

use App\ApiServices\Validation\StepValidator;
use App\Rules\Miscs\DependentMiscsSettingsRule;
use App\Rules\Miscs\SingleMiscDataBaseRule;


final class DependencyValidator extends StepValidator
{

    /**
     * Валидация входных параметров
     *
     * Логирует ошибки
     *
     */
    public function inputParametersValidation(): void
    {
        $this->validateClientInput($this->req->all(), [
            'selectedId'     => ['required', 'numeric'],
            'subMiscAliases' => ['required', 'string'],
            'mainMiscAlias'  => [
                'bail',
                'required',
                'string',
                new DependentMiscsSettingsRule($this->req->subMiscAliases ?? -1),
                new SingleMiscDataBaseRule($this->req->selectedId ?? -1)
            ]
        ]);
    }
}
