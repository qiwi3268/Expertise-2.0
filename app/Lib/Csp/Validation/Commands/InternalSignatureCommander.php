<?php

declare(strict_types=1);

namespace App\Lib\Csp\Validation\Commands;


/**
 * Формирует массив команды для проверки встроенной подписи
 *
 */
final class InternalSignatureCommander extends SignatureValidationCommander
{


    /**
     * Конструктор класса
     *
     * @param string $internalSignatureFile абсолютный путь в ФС сервера к файлу встроенной подписи
     */
    public function __construct(
        private string $internalSignatureFile,
    ) {
        parent::__construct($internalSignatureFile);
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
            '-attached',    // флаг встроенной подписи
            '-mca',         // поиск сертификатов осуществляется в хранилище компьютера CA
            '-all',         // использовать все найденные сертификаты
            '-errchain',    // завершать выполнение с ошибкой, если хотя бы один сертификат не прошел проверку
            '-verall',      // проверять все подписи
            $this->internalSignatureFile,
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
            '-attached',    // флаг встроенной подписи
            '-nochain',     // не проверять цепочки найденных сертификатов
            '-verall',      // проверять все подписи
            $this->internalSignatureFile,
        ];
    }
}
