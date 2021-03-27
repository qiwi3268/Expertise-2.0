<?php


namespace App\Lib\Filesystem;

use App\Exceptions\Lib\Filesystem\FilesystemException;

use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Lib\Singles\SettingsChecker;


/**
 * Представляет value object для параметров "простого" файлового хранилища
 *
 * Под простым понимается хранилище, не имеющее поддиректории относительно точки входа
 *
 */
class SimpleStorageParameters
{

    /**
     * Конструктор класса
     *
     * @param string $diskName имя диска хранилища
     * @throws FilesystemException
     */
    public function __construct(private string $diskName)
    {
        if (!SettingsChecker::existFilesystemDisk($diskName)) {
            throw new FilesystemException("Файловое хранилище по имени диска: '{$diskName}' несуществует");
        }
    }


    /**
     * Возвращает имя диска хранилища
     *
     * @return string
     */
    public function getDiskName(): string
    {
        return $this->diskName;
    }


    /**
     * Возвращает абсолютный путь к хранилищу в ФС сервера
     *
     * @return string
     */
    public function getDiscPath(): string
    {
        return config("filesystems.disks.{$this->diskName}.root");
    }


    /**
     * Возвращает адаптер файловой системы
     *
     * Это объект, возвращаемый фасадом Storage при установке диска
     *
     * @return FilesystemAdapter
     */
    public function getFilesystemAdapter(): FilesystemAdapter
    {
        return Storage::disk($this->diskName);
    }
}
