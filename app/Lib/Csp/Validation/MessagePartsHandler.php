<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation;


/*
 * Предназначен для обработки сформированных частей сообщения
 *
 */
final class MessagePartsHandler
{

    /**
     * Обработчик сформированных частей сообщения
     *
     * @param string $part
     * @return array
     */
    public function handle(string $part): array
    {
        $part = trim($part,' ');

        if ($part === '') return [];

        // Обработка ситуаций, когда из-за отсутствия прогресс-бара проверки подписи некоторые части сообщения
        // окажутся в одной строке, т.к. символ переноса строк принадлежит прогресс-бару.
        if (
            pm('/Signature verifying\.\.\..*(\[ErrorCode.*)$/', $part, $m) ||
            pm('/Signature verifying\.\.\..*(Error.*)$/', $part, $m)
        ) {
            return [$m];
        }

        if (str_contains_any($part, [
            '"Crypto-Pro"',
            'Command prompt Utility',
            'Folder',
            'Signature verifying',
            'CSPbuild'
        ])) {
            return [];
        }
        return [$part];
    }
}
