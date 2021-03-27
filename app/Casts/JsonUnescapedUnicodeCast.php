<?php

declare(strict_types=1);

namespace App\Casts;

use JsonException;
use RuntimeException;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;


final class JsonUnescapedUnicodeCast implements CastsAttributes
{

    /**
     * Декодирует строку json
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return array
     * @throws RuntimeException
     */
    public function get($model, $key, $value, $attributes): array
    {
        try {
            return json_decode($value, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException("Ошибка при декодировании строки json из БД: '{$value}'", 0, $e);
        }
    }


    /**
     * Возвращает json-представление данных
     *
     * @param Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return string
     * @throws RuntimeException
     */
    public function set($model, $key, $value, $attributes): string
    {
        try {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException("Ошибка при кодировании строки в json: '{$value}'", 0, $e);
        }
    }
}