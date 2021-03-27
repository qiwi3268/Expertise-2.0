<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use LogicException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Lib\Formats\ApiResponse\ApiResponseFormatter;


/**
 * Предназначен для удобного завершения программы при возникновении ошибок
 *
 * Логирует сообщения
 *
 * Приводит все серверные ответы к единообразному json формату
 *
 */
abstract class ApiException extends LogicException
{

    /**
     * Конструктор класса
     *
     * @param ExceptionContext $context
     */
    public function __construct(private ExceptionContext $context)
    {
        parent::__construct($context->message);
    }


    /**
     * Логирование исключения
     *
     */
    public function report(): void
    {
        Log::channel($this->getLogChannel())->error($this->context->message, [
            'errors'  => $this->context->errors,
            'code'    => $this->context->code,
            'context' => $this->context->context
        ]);
    }


    /**
     * Json ответ клиенту
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        $body = ApiResponseFormatter::getErrorBody(
            $this->context->message,
            $this->context->errors,
            $this->context->code,
            $this->context->meta
        );
        return response()->json($body, $this->getStatusCode());
    }


    /**
     * Возвращает название канала логирования
     *
     * @return string
     */
    abstract protected function getLogChannel(): string;


    /**
     * Возвращает http код ответа
     *
     * @return int
     */
    abstract protected function getStatusCode(): int;
}
