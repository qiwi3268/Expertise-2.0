<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Files;

use App\ApiServices\Validation\StepValidator;

use App\Rules\StarPathRule;


final class InternalSignatureValidationValidator extends StepValidator
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
            'internalSignatureStarPath' => ['required', new StarPathRule],
        ]);
    }
}
