<?php

declare(strict_types=1);

namespace App\ApiServices\Validation\Files;

use App\Exceptions\Api\ExceptionContext;

use App\Http\ApiControllers\ApiController;
use App\ApiServices\Validation\StepValidator;

use App\Rules\MappingsRule;
use App\Rules\Files\UploadedFileRule;
use App\Rules\Files\FileServerUploadRule;
use App\Rules\Files\FileNotEmptyRule;


final class UploadValidator extends StepValidator
{

    /**
     * Валидация общих входных параметров
     *
     * Логирует ошибки
     *
     */
    public function commonInputParametersValidation(): void
    {
        $this->validateClientInput($this->req->all(), [
            'mappings'         => ['required', new MappingsRule],
            'targetDocumentId' => ['required', 'numeric'],
            'files'            => ['bail', 'required', 'array' , 'min:1'],
        ]);
    }


    /**
     * Валидация входных параметров загрузчика
     *
     * Логирует ошибки
     *
     * @param array $rules правила загрузчика
     */

    public function uploaderInputParametersValidation(array $rules): void
    {
        if (!empty($rules) && $this->validationFails($this->req->all(), $rules)) {
            ExceptionContext::create(
                'Ошибка при валидации входных параметров загрузчика',
                $this->lastErrors,
                ApiController::CLIENT_INVALID_INPUT_ERROR_CODE
            )->throwClientException();
        }
    }


    /**
     * Валидация на ошибки при загрузке файлов на сервер
     *
     * Логирует ошибки
     *
     */
    public function serverUploadedFilesValidation(): void
    {
        if ($this->validationFails($this->req->file(), [
            'files.*' => [
                new UploadedFileRule,
                new FileServerUploadRule
            ]
        ])) {
            ExceptionContext::create(
                'Ошибка при загрузке файлов на сервер',
                $this->lastErrors
            )->throwServerException();
        }
    }


    /**
     * Валидация на корректность загруженных файлов (расширение, размер, и т.д.)
     *
     * Логирует ошибки
     *
     * @param array $rules правила загрузчика
     */
    public function uploaderFilesValidation(array $rules): void
    {
        if ($this->validationFails($this->req->file(), [
            'files.*' => [
                'bail',
                new FileNotEmptyRule,
                ...$rules
            ]
        ])) {
            ExceptionContext::create(
                'Ошибка при проверке загруженных файлов',
                $this->lastErrors,
                ApiController::CLIENT_INVALID_INPUT_ERROR_CODE
            )->throwClientException();
        }
    }
}
