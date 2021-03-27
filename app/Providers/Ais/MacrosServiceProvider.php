<?php

declare(strict_types=1);

namespace App\Providers\Ais;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


final class MacrosServiceProvider extends ServiceProvider
{

    /**
     *
     * @return void
     */
    public function register(): void
    {
    }


    /**
     * Предназначен для внедрения макросов
     *
     * @return void
     */
    public function boot(): void
    {

        /**
         * Проверяет наличие хотя бы одного элемента из входящего массива в исходной коллекции
         *
         * Проверка производится по значениям и только на первом уровне вложенности
         *
         */
        Collection::macro('hasAny', function (array $arr) {
            foreach ($this as $item) {
                foreach ($arr as $needle) {
                    if ($item === $needle) return true;
                }
            }
            return false;
        });


        /**
         * Возвращает первый ключ исходной коллекции, где вложенный элемент по указанному ключу равен указанному значению
         * или null, в случае отсутствия элемента
         *
         */
        Collection::macro('firstKeyWhere', function (string $key, mixed $value) {
            foreach ($this as $collectionKey => $assoc) {
                if (array_key_exists($key, $assoc) && ($assoc[$key] === $value)) return $collectionKey;
            }
            return null;
        });


        /**
         * Возвращает коллекцию, в которой по ключам исходной коллекции находятся эти же ключи
         *
         */
        Collection::macro('hashKeys', function () {
            $result = [];
            foreach ($this as $key => $unused) $result[$key] = $key;
            return collect($result);
        });


        /**
         * Проверяет имеется ли разница в ключах ассоциативных массивов
         *
         * array - индексный массив с ассоциативными внутри
         */
        Arr::macro('hasDiffKeys', function (array $array) {

            if (empty($array)) return false;

            $a = [];

            foreach ($array as $assoc) {
                foreach ($assoc as $key => $unused) {
                    isset($a[$key]) ? $a[$key]++ : $a[$key] = 1;
                }
            }

            // Если все массивы с ассоциированными данными имеют одинаковую структру,
            // то после array_flip они переворачиваются в один ключ
            return count(array_flip($a)) != 1;
        });


        /**
         * Преобразует строку к snake нотации с учетом чисел
         */
        Str::macro('numericSnake', function ($value) {

            return Str::of($value)
                ->snake()
                ->replaceMatches('/\d+/', fn ($match) => "_{$match[0]}_")
                ->replaceMatches('/_{2,}/', fn ($match) => '_')
                ->ltrim('_')->rtrim('_');
        });
    }
}
