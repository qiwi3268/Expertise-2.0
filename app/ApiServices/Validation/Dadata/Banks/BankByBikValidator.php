<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Dadata\Banks;

use App\ApiServices\Validation\StepValidator;
use App\Lib\Singles\PatternLibrary;
use App\Rules\RegularExpressionRule;


final class BankByBikValidator extends StepValidator
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
            'bik' => ['required', new RegularExpressionRule('БИК', PatternLibrary::BIK)],
        ]);
    }
}