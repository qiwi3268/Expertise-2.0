<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Files;

use App\ApiServices\Validation\StepValidator;
use App\Rules\StarPathRule;


final class ExternalSignatureValidationValidator extends StepValidator
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
            'originalStarPath'          => ['required', new StarPathRule],
            'externalSignatureStarPath' => ['required', new StarPathRule],
        ]);
    }
}
