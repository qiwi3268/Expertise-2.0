<?php

declare(strict_types=1);

namespace App\Lib\Csp\Hashing\Commands;

use App\Lib\Csp\CspCommander;


/**
 * Предназначен для получения команд создания hash файла для исходного файла
 *
 */
final class HashCommander extends CspCommander
{

    /**
     * Конструктор класса
     *
     * @param string $hashDir абсолютный путь в ФС сервера, куда будет сохранен результирующий hash файл
     * @param string $hashAlg алгоритм хэширования
     * @param string $filePath абсолютный путь в ФС сервера к исходному файлу
     */
    public function __construct(
        private string $hashDir,
        private string $hashAlg,
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
            parent::CPROCSP,
            '-hash',
            '-dir',
            $this->hashDir,
            '-provtype',    // тип криптопровайдера
            '80',
            '-hashAlg',     // алгоритм хэширования
            $this->hashAlg,
            '-hex',         // сохранить хэш файла в виде шестнадцатеричной строки
            $this->filePath
        ];
    }
}
