<?php


namespace App\Exceptions\Api;


/**
 * Аналог {@see \App\Exceptions\Api\ClientException} без логирования
 *
 * Соответственно, из ExceptionContext не будет использован logContext
 *
 */
final class ClientExceptionWithoutReport extends ClientException
{
    /**
     * Отключенное логирование исключения
     *
     */
    public function report(): void
    {
    }
}
