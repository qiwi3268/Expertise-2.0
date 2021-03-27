<?php

declare(strict_types=1);

namespace App\Lib\Filesystem;

use App\Exceptions\Lib\Filesystem\FilesystemException;


/**
 * Представляет value object для параметров файлового хранилища, имеющего поддиректории
 *
 */
final class StorageParameters extends SimpleStorageParameters
{


    /**
     * Конструктор класса
     *
     * @param string $diskName имя диска хранилища
     * @param string $subDirectory поддиректория относительно точки входа хранилища.
     * Начинается и заканчивается без '/'
     * @throws FilesystemException
     */
    public function __construct(private string $diskName, private string $subDirectory)
    {
        parent::__construct($diskName);

        if (!pm('/^\d+(\/\d+)*$/', $this->subDirectory)) {
            throw new FilesystemException('Указана поддиректория неверного формата');
        }
    }


    /**
     * Возвращает поддиректорию
     *
     * @return string
     */
    public function getSubDirectory(): string
    {
        return $this->subDirectory;
    }
}
