<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use Illuminate\Support\Arr;


/*
 * Вспомогательный класс для различных проверок, связанных с пакетом settings
 *
 * Философия класса преимущественно в методах, которые возвращают bool
 *
 */
final class SettingsChecker
{

    /**
     * Проверяет существование именени диска файлового хранилища
     *
     * @param string $diskName
     * @return bool
     */
    public static function existFilesystemDisk(string $diskName): bool
    {
        return Arr::exists(config('filesystems.disks'), $diskName);
    }
}
