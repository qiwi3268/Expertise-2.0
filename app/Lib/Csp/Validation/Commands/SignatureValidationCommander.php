<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\Commands;

use App\Lib\Csp\CspCommander;


/**
 * Предназначен для получения команд проверки подписей
 *
 */
abstract class SignatureValidationCommander extends CspCommander
{

    /**
     * Конструктор класса
     *
     * @param string $cryptographicFile абсолютный путь в ФС сервера
     * к файлу с открепленной или встроенной подписью
     */
    public function __construct(private string $cryptographicFile)
    {}


    /**
     * Возвращает абсолютный путь в ФС сервера к криптографическому файлу
     *
     * @return string
     */
    public function getCryptographicFilePath(): string
    {
        return $this->cryptographicFile;
    }


    /**
     * Возвращает массив команды проверки подписи с цепочкой сертификатов
     *
     * @return array
     */
    abstract public function getChainCommand(): array;


    /**
     * Возвращает массив команды проверки подписи без цепочки сертификатов
     *
     * @return array
     */
    abstract public function getNoChainCommand(): array;
}
