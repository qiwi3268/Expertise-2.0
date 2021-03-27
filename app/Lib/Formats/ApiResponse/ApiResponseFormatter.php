<?php

declare(strict_types=1);

namespace App\Lib\Formats\ApiResponse;


/**
 * Предоставляет единый формат ответов api
 *
 */
final class ApiResponseFormatter
{

    /**
     * Закрытый конструктор класса
     *
     */
    private function __construct() {}


    /**
     * Возвращает тело ответа с ошибкой
     *
     * @param string $message
     * @param array $errors
     * @param string $code
     * @param array $meta
     * @return array
     */
    public static function getErrorBody(
        string $message,
        array $errors,
        string $code,
        array $meta
    ): array {
        return [
            'message' => $message,
            'errors'  => $errors,
            'code'    => $code,
            'meta'    => $meta,
        ];
    }


    /**
     * Возвращает тело успешного ответа
     *
     * @param string $message
     * @param array $data
     * @param array $meta
     * @return array
     */
    public static function getSuccessBody(
        string $message,
        array $data,
        array $meta
    ): array {
        return [
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta
        ];
    }
}
