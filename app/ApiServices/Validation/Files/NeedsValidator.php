<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Files;

use App\ApiServices\Validation\StepValidator;
use App\Rules\Arrays\StringListArrayRule;
use App\Rules\StarPathRule;


final class NeedsValidator extends StepValidator
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
            'toSave'     => ['present', new StringListArrayRule],
            'toSave.*'   => [new StarPathRule],
            'toDelete'   => ['present', new StringListArrayRule],
            'toDelete.*' => [new StarPathRule]
        ]);
    }
}
