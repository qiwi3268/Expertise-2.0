<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Files;

use App\ApiServices\Validation\StepValidator;


final class CheckValidator extends StepValidator
{

    /**
     * Валидация входных параметров
     *
     * Логирует ошибки
     *
     */
    public function inputParametersValidation(): void
    {
        // Без валидации строки starPath, т.к. в контроллере выполняется полная валидация
        $this->validateClientInput($this->req->all(), [
            'starPath' => ['required'],
        ]);
    }
}