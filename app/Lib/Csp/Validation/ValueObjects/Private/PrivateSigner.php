<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\ValueObjects\Private;

use App\Lib\ValueObjects\Fio;


/**
 * Представляет value object подписанта из сообщения
 *
 * Предназначен только для внутреннего использования
 *
 */
final class PrivateSigner
{
    /**
     * Представляет собой индекс из массива PrivateValidationResult
     */
    public int $index;

    /**
     * Объект фио
     */
    public Fio $fio;

    /**
     * Результат проверки
     */
    public bool $result;

    /**
     * Сообщение с результатом проверки
     */
    public string $message;
}
