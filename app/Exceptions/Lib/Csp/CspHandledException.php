<?php


namespace App\Exceptions\Lib\Csp;


/**
 * Является "обрабатываемым" исключением, т.е. дальнейшее выполнение программы может корректно
 * продолжиться путем обработки ErrorCode и т.д.
 *
 */
final class CspHandledException extends CspException
{
}
