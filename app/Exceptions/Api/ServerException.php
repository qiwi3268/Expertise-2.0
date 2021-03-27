<?php


namespace App\Exceptions\Api;


/**
 * Под "серверной" ошибкой понимаются все ошибки,
 * которые произошли после валидации клиентских входных данных
 *
 * В любых случаях возвращаем код ошибки 500 - Internal Server Error
 *
 */
final class ServerException extends ApiException
{

    /**
     * Реализация абстрактного метода
     *
     * @return string
     */
    protected function getLogChannel(): string
    {
        return 'api_server_errors';
    }


    /**
     * Реализация абстрактного метода
     *
     * @return int
     */
    protected function getStatusCode(): int
    {
        return 500;
    }
}
