<?php

declare(strict_types=1);

namespace App\Lib\Filesystem;

use Illuminate\Support\Facades\Storage;
use App\Repositories\Files\FileRepository;


/**
 * Представляет value object для starPath
 *
 */
final class StarPath
{
    private FileRepository $rep;


    /**
     * Конструктор класса
     *
     * @param StorageParameters $storageParameters параметры файлового хранилища
     * @param string $hashName хэш имя файла
     */
    public function __construct(
        private StorageParameters $storageParameters,
        private string $hashName
    ) {
        $this->rep = new FileRepository;
    }


    /**
     * Возвращает хэш имя файла
     *
     * @return string
     */
    public function getHashName(): string
    {
        return $this->hashName;
    }


    /**
     * Прокси вызов для получения имени диска хранилища
     *
     * @return string
     */
    public function getDiskName(): string
    {
        return $this->storageParameters->getDiskName();
    }


    /**
     * Прокси вызов для получения поддиректории
     *
     * @return string
     */
    public function getSubDirectory(): string
    {
        return $this->storageParameters->getSubDirectory();
    }


    /**
     * Возвращает путь к файлу относительно точки входа хранилища
     *
     * @return string
     */
    public function getPath():string
    {
        return "{$this->getSubDirectory()}/{$this->hashName}";
    }


    /**
     * Возвращает абсолютный путь к файлу
     *
     * @return string
     */
    public function getAbsPath(): string
    {
        return $this->storageParameters->getDiscPath() . '/' . $this->getPath();
    }


    /**
     * Верифицирует данные с БД
     *
     * @param bool $mustBeNeeds должен ли у файла быть установлен флаг "нужности"
     * @return bool
     */
    public function verifyWithDatabase(bool $mustBeNeeds): bool
    {
        $s = $this->getSubDirectory();
        $h = $this->getHashName();

        return $mustBeNeeds
            ? $this->rep->existsBySubDirectoryAndHashNameAndIsNeeds($s, $h, true)
            : $this->rep->existsBySubDirectoryAndHashName($s, $h);
    }


    /**
     * Верифицирует данные с ФС сервера
     *
     * @return bool
     */
    public function verifyWithFilesystem(): bool
    {
        return Storage::disk($this->getDiskName())->exists($this->getPath());
    }


    /**
     * Предназначен для преобразования к строке для отладки
     *
     * @return string
     */
    public function __toString(): string
    {
        return "hash_name: '{$this->hashName}'"
            . " disk_name: '{$this->getDiskName()}'"
            . " sub_directory: '{$this->getSubDirectory()}'";
    }
}
