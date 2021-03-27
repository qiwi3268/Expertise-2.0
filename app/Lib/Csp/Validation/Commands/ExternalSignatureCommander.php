<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\Commands;


/**
 * Формирует массив команды для проверки открепленной подписи
 *
 */
final class ExternalSignatureCommander extends SignatureValidationCommander
{


    /**
     * Конструктор класса
     *
     * @param string $originalFile абсолютный путь в ФС сервера к исходному файлу
     * @param string $externalSignatureFile абсолютный путь в ФС сервера к файлу открепленной подписи
     */
    public function __construct(
        private string $originalFile,
        private string $externalSignatureFile
    ) {
        parent::__construct($externalSignatureFile);
    }


    /**
     * Реализация абстрактного метода
     *
     * @return array
     */
    public function getChainCommand(): array
    {
        return [
            parent::CPROCSP,
            '-verify',
            '-detached',    // флаг открепленной подписи
            '-mca',         // поиск сертификатов осуществляется в хранилище компьютера CA
            '-all',         // использовать все найденные сертификаты
            '-errchain',    // завершать выполнение с ошибкой, если хотя бы один сертификат не прошел проверку
            '-verall',      // проверять все подписи
            $this->originalFile,
            $this->externalSignatureFile
        ];
    }


    /**
     * Реализация абстрактного метода
     *
     * @return array
     */
    public function getNoChainCommand(): array
    {
        return [
            parent::CPROCSP,
            '-verify',
            '-detached',    // флаг открепленной подписи
            '-nochain',     // не проверять цепочки найденных сертификатов
            '-verall',      // проверять все подписи
            $this->originalFile,
            $this->externalSignatureFile
        ];
    }
}
