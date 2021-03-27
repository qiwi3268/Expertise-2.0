<?php


namespace App\Exceptions\Api;


/**
 * Под "клиентской" ошибкой понимается ошибка JS.
 *
 * Варианты:
 * - Сервер сформировал неверные данные для html страницы
 * - Внутренняя ошибка при обработке данных Js
 * - Js дал сохранить данные при незаполненных обязательных полях и т.д.
 * - Пользователь изменил html код
 *
 * В любых случаях возвращаем код ошибки 422 - Unprocessable Entity
 *
 */
class ClientException extends ApiException
{

    /**
     * Реализация абстрактного метода
     *
     * @return string
     */
    protected function getLogChannel(): string
    {
        return 'api_client_errors';
    }


    /**
     * Реализация абстрактного метода
     *
     * @return int
     */
    protected function getStatusCode(): int
    {
        return 422;
    }
}
