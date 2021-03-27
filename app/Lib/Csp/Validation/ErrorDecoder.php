<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation;


/**
 * Предназначен для определения произошедшей ситации по коду ошибки
 *
 */
final class ErrorDecoder
{

    /**
     * Конструктор класса
     *
     * @param string $errorCode код ошибки
     */
    public function __construct(private string $errorCode)
    {}


    /**
     * Проверка на "Недействительный тип криптографичесого сообщения"
     *
     * @return bool
     */
    public function isInvalidMessageType(): bool
    {
        return $this->errorCode === '0x80091004';
    }


    /**
     * Проверка на "Передан некорректный параметр"
     *
     * Для встроенной подписи ошибка означает: проверяется файл открепленной подписи,
     * либо просто некорректная подпись (например, файл изменен)
     *
     * @return bool
     */
    public function isIncorrectParameter(): bool
    {
        return $this->errorCode === '0x00000057';
    }


    /**
     * Проверка на "Потоковое криптографическое сообщение не готово для возврата данных"
     *
     * Для встроенной подписи ошибка означает: был передан пустой файл
     *
     * @return bool
     */
    public function isCSPNotReadyToReturnData(): bool
    {
        return $this->errorCode === '0x80091010';
    }


    /**
     * Проверка на "Не удалось открыть файл"
     *
     * Для открепленной подписи ошибка означает: был передан пустой файл
     *
     * @return bool
     */
    public function isCantOpenFile(): bool
    {
        return $this->errorCode === '0x20000065';
    }


    /**
     * Проверка на "Проверка подписи не началась"
     *
     * Для открепленной подписи ошибка означает: проверяется файл без подписи и файл без подписи
     *
     * @return bool
     */
    public function isSignatureVerifyingNotStarted(): bool
    {
        return $this->errorCode === '0xffffffff';
    }
}
