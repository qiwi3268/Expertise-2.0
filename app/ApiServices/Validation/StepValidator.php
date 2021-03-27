<?php

declare(strict_types=1);

namespace App\ApiServices\Validation;

use App\Exceptions\Api\ExceptionContext;
use App\Exceptions\Api\ClientException;

use App\Http\ApiControllers\ApiController;
use App\Http\Requests\AppRequest;


/*
 * Предназначен для валидации клиентских данных
 *
 * Отличается от стандартного валидатора в ту сторону, что направлен на "пошаговую" валидацию,
 * то есть может выполняться многократно в течение работы контроллера
 *
 */
abstract class StepValidator
{

    protected array $lastErrors = [];


    /**
     * Конструктор класса
     *
     * @param AppRequest $req
     */
    public function __construct(protected AppRequest $req)
    {}


    /**
     * Обёртка над стандартной валидацией
     *
     * @param array $data
     * @param array $rules
     * @param bool $interrupt следует ли прерывать валидацию оставшихся ключей,
     * если валидация предшествующего завершилась с ошибкой
     * ключа завершилась с ошибкой
     * @return bool true, если валидация прошла с ошибкой
     */
    public function validationFails(array $data, array $rules, bool $interrupt = true): bool
    {
        $this->lastErrors = [];

        // Каждое правило валидируется отдельно, для возможности прерывания
        foreach ($rules as $name => $array) {

            $validator = validator($data, [$name => $array]);

            if ($validator->fails()) {

                $this->lastErrors[] = $validator->errors()->all();
                if ($interrupt) break;
            }
        }
        return !empty($this->lastErrors);
    }


    /**
     * Валидирует ввод пользователя и завершает программу в случае непройденной валидации
     *
     * @param array $data
     * @param array $rules
     * @param bool $interrupt
     * @throws ClientException
     */
    public function validateClientInput(array $data, array $rules, bool $interrupt = true): void
    {
        if ($this->validationFails($data, $rules, $interrupt)) {

            ExceptionContext::create(
                'Ошибка при валидации общих входных параметров',
                $this->lastErrors,
                ApiController::CLIENT_INVALID_INPUT_ERROR_CODE
            )->throwClientException();
        }
    }
}
