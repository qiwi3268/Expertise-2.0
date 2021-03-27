<?php

declare(strict_types=1);

namespace App\Lib\Filesystem;

use App\Exceptions\Lib\Filesystem\FilesystemException;

use Illuminate\Support\Str;


/**
 * Предназначен для обработки starPath
 *
 * starPath - это название для строки формата:
 * - имя диска хранилища
 * - *
 * - путь к файлу, включая поддиректорию
 *
 */
final class StarPathHandler
{
    /**
     * 1 группа - имя диска хранилища (латинские буквы в нижнем регистре)
     * 2 группа - поддиректория (цифры и /)
     * 3 группа - хэш имя файла
     */
    private const PATTERN = '/^([a-z]+)\*([0-9\/]+)\/(.+)$/';


    /**
     * Создает строку starPath
     *
     * @param StorageParameters $vo
     * @param string $hashName хэш имя файла
     * @return string
     */
    public static function createString(StorageParameters $vo, string $hashName): string
    {
        return "{$vo->getDiskName()}*{$vo->getSubDirectory()}/{$hashName}";
    }


    /**
     * Создает непроверенный vo
     *
     * @param string $starPath
     * @return StarPath
     * @throws FilesystemException
     */
    public static function createUnvalidated(string $starPath): StarPath
    {
        pm(self::PATTERN, $starPath, $m);
        [$diskName, $subDirectory, $hashName] = $m;

        return new StarPath(new StorageParameters($diskName, $subDirectory), $hashName);
    }


    /**
     * Создает vo, у которого проверена starPath строка
     *
     * @param string $starPath
     * @return StarPath
     * @throws FilesystemException
     */
    public static function createStringValidated(string $starPath): StarPath
    {
        if (!self::stringValidate($starPath)) {
            throw new FilesystemException("Строка starPath: '{$starPath}' некорректна");
        }
        return self::createUnvalidated($starPath);
    }


    /**
     * Создает vo, который полностью проверен
     *
     * @param string $starPath
     * @param bool $mustBeNeeds должен ли у файла быть установлен флаг "нужности"
     * @return StarPath
     * @throws FilesystemException
     */
    public static function createFullValidated(string $starPath, bool $mustBeNeeds): StarPath
    {
        self::fullValidate($starPath, $mustBeNeeds);
        return self::createUnvalidated($starPath);
    }


    /**
     * Проверяет только starPath строку
     *
     * @param string $starPath
     * @return bool
     */
    public static function stringValidate(string $starPath): bool
    {
        return pm(self::PATTERN, $starPath, $m) && Str::isUuid($m[2]);
    }


    /**
     * Полная проверка starPath
     *
     * @param string $starPath
     * @param bool $mustBeNeeds должен ли у файла быть установлен флаг "нужности"
     * @throws FilesystemException
     */
    public static function fullValidate(string $starPath, bool $mustBeNeeds): void
    {
        // Проверка имени диска файлового хранилища опущена, т.к. она происходит в конструкторе класса SimpleStorageParameters
        $obj = self::createStringValidated($starPath);

        if (!$obj->verifyWithDatabase($mustBeNeeds)) {
            throw new FilesystemException("По указанному starPath: '{$starPath}' не найдено подходящей записи в таблице");
        }
        if (!$obj->verifyWithFilesystem()) {
            throw new FilesystemException("Файл по указанному starPath: '{$starPath}' физически отсутствует в ФС сервера");
        }
    }
}
