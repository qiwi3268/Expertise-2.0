<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Dadata\Organizations;

use App\ApiServices\Validation\StepValidator;
use App\Lib\Singles\PatternLibrary;
use App\Rules\RegularExpressionRule;


final class OrganizationByPersInnValidator extends StepValidator
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
            'persInn' => ['required', new RegularExpressionRule('ИНН индивидуального предпринимателя', PatternLibrary::PERS_INN)],
        ]);
    }
}