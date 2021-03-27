<?php

declare(strict_types=1);

namespace App\Lib\Csp;

use App\Exceptions\Lib\Csp\CspParsingException;


/**
 * Общий тип для классов разбора вывода исполняемой команды
 *
 */
class MessageParser
{

    /**
     * Код, соответствующий успешному выполнению команды
     *
     */
    public const OK_ERROR_CODE = '0x00000000';

    protected const ERROR_CODE_PATTERN = '/\[ErrorCode:\s*(.+)]/';


    /**
     * Возвращает код ошибки из сообщения
     *
     * @param string $message
     * @return string
     * @throws CspParsingException
     */
    public function getErrorCode(string $message): string
    {
        if (!pm(self::ERROR_CODE_PATTERN, $message, $errorCode)) {
            throw new CspParsingException('В полученном сообщении отсутствует ErrorCode');
        }
        return $errorCode;
    }


    /**
     * Соответствует ли код ошибки успешному выполнению команды
     *
     * @param string $message
     * @return bool
     * @throws CspParsingException
     */
    public function isOkErrorCode(string $message): bool
    {
        return self::OK_ERROR_CODE == $this->getErrorCode($message);
    }
}
