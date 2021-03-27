<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Throwable;
use Exception;


/**
 * Предназначен для объединения ошибок во время сохранения моделей
 *
 */
final class SaveModelException extends Exception
{

    /**
     * Конструктор класса
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
