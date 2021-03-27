<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;


/**
 * Исключение, связанное с ошибкой при запросе к внешнему API
 *
 * Это может быть связано с кодом ответа 4xx или 5xx, который Guzzle преобразовывает в исключение
 *
 * Логирование исключения происходит на уровне laravel обработчика
 *
 */
final class ExternalApiException extends Exception
{

    /**
     * Выбрасывает новое исключение на основе предыдущего
     *
     * @param string $apiName имя api
     * @param Throwable $e
     * @throws ExternalApiException
     */
    public static function rethrow(string $apiName, Throwable $e): void
    {
        throw new self("Ошибка при запросе к API '{$apiName}': {$e->getMessage()}", $e->getCode(), $e);
    }


    /**
     * Оболочка для вызова api
     *
     * @param callable $callback
     * @param string $apiName
     * @return mixed результат callback функции
     * @throws ExternalApiException
     */
    public static function shellToCallApi(callable $callback, string $apiName): mixed
    {
        try {
            $result = $callback();
        } catch (Throwable $e) {
            self::rethrow($apiName, $e);
        }
        return $result;
    }
}
