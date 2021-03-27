<?php

declare(strict_types=1);

namespace App\Lib\Cache;

use Throwable;
use InvalidArgumentException;


/**
 * Единая точка для формриования ключей кэш хранилища
 *
 */
final class KeyNaming
{
    
    
    /**
     * Генерирует ключ, по которому будут храниться кэшированные данные
     *
     * Принимаемые массивы должны быть конвертируемы в json
     *
     * @param array $who идентификация того, кто кэширует.
     * В общем случае - имя класса и метода, который выполняет менеджмент кэша.
     * @param array $what идентификация того, что кэшируется.
     * В общем случае - имя класса, метода и параметры, которые передаются в метод.
     * @return string
     * @throws InvalidArgumentException
     */
    public static function create(array $who, array $what): string
    {
        try {
            return json_encode($who, JSON_THROW_ON_ERROR) . '_' . json_encode($what, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new InvalidArgumentException("Ошибка при преобразовании параметров в строку: '{$e->getMessage()}'", 0, $e);
        }
    }
}
