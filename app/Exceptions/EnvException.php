<?php

declare(strict_types=1);

namespace App\Exceptions;

use LogicException;


/**
 * Исключение, связанное с ошибкой при работе с env файлом
 *
 */
final class EnvException extends LogicException
{
}
