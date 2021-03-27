<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Dadata\Organizations;

use App\ApiServices\Validation\StepValidator;
use App\Lib\Singles\PatternLibrary;
use App\Rules\RegularExpressionRule;


final class OrganizationByOrgInnValidator extends StepValidator
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
            'orgInn' => ['required', new RegularExpressionRule('ИНН юридического лица', PatternLibrary::ORG_INN)],
        ]);
    }
}