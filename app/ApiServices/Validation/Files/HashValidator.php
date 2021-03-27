<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Files;

use App\ApiServices\Validation\StepValidator;

use App\Rules\SignAlgorithmRule;
use App\Rules\StarPathRule;


final class HashValidator extends StepValidator
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
            'signAlgorithm' => ['required', new SignAlgorithmRule],
            'starPath'      => ['required', new StarPathRule],
        ]);
    }
}
