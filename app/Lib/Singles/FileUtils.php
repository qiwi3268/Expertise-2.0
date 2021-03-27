<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;


/**
 * Вспомогательный класс для работы с файлами
 *
 */
final class FileUtils
{

    /**
     * Возвращает человекопонятный размер файла
     *
     * @param int $bytes размер файла в байтах
     * @return string строка формата: 20,65 Мб
     */
    public static function getHumanFileSize(int $bytes): string
    {
        if ($bytes < 0) throw new InvalidArgumentException("Размер файла не может быть меньше 0");
        if ($bytes == 0) return '0 Б';

        foreach (array_reverse(['Б', 'Кб', 'Мб', 'Гб', 'Тб'], true) as $exp => $label) {

            if ($bytes >= ($value = pow(1024, $exp))) {
                return str_replace('.', ',', round(($bytes / $value), 2) . " {$label}");
            }
        }
    }
}
