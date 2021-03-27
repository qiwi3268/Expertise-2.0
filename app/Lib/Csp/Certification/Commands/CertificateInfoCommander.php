<?php

declare(strict_types=1);

namespace App\Lib\Csp\Certification\Commands;

use App\Lib\Csp\CspCommander;


/**
 * Предназначен для получения команд информации о сертификате
 *
 */
final class CertificateInfoCommander extends CspCommander
{

    /**
     * Конструктор класса
     *
     * @param string $filePath абсолютный путь в ФС сервера к файлу
     * встроенной или открепленной подписи
     */
    public function __construct(
        private string $filePath
    ) {}


    /**
     * Возвращает массив команды
     *
     * @return array
     */
    public function getCommand(): array
    {
        return [
            parent::CERTMGR,
            '-list',        // показать сертификаты
            '-file',        // сертификаты из файла
            $this->filePath
        ];
    }
}
