<?php

declare(strict_types=1);

use Illuminate\Support\Arr;



/**
 * Возвращает первый массив, в котором элемент,
 * расположенный по пути точечной нотации, имеет нужное значение
 *
 * @param array $array
 * @param string $dot путь к элементу в точечной нотации.
 * Начинается от второго уровня вложенности и включает в себя ключ целевого элемента.
 * @param mixed $value
 * @param null|string|array $unpackKeys
 * null - распаковка массива по ключам не требуется
 * @return mixed
 * @throws InvalidArgumentException функция должна выбрасывать исключение, поскольку иначе
 * невозможно отличить отсутствие вхождения от значения null/false
 */
function arr_first(array $array, string $dot, mixed $value, null|string|array $unpackKeys = null): mixed
{
    if(pm('/(.+)\.(.+)/u', $dot, $matches)) {
        // Перезаписываем точечную нотацию до предпоследнего элемента.
        // Последний элемент в таком случае является ключом, по которому сравнивается значение value.
        // Таким образом, Arr::get вызывается только один раз для поиска массива, а не только элемента
        [$dot, $targetKey] = $matches;
    }

    foreach ($array as $assoc) {

        if (!is_array($assoc)) {
            throw new InvalidArgumentException('Элемент не является массивом', 1);
        }
        if (isset($targetKey)) {

            $searchArray = Arr::get($assoc, $dot);

            if (array_key_exists($targetKey, $searchArray) && $searchArray[$targetKey] === $value) {

                return is_null($unpackKeys) ? $searchArray : arr_unpack($searchArray, $unpackKeys);
            }
        } elseif (array_key_exists($dot, $assoc) && $assoc[$dot] === $value) {

            return is_null($unpackKeys) ? $assoc : arr_unpack($assoc, $unpackKeys);
        }
    }
    throw new InvalidArgumentException("Требуемый элемент не существует в массиве", 2);
}


/**
 * Проверяет существование элемента, который расположен
 * по пути точечной нотации и имеет нужное значение
 *
 * @param array $array
 * @param string $dot
 * @param mixed $value
 * @return bool
 * @throws InvalidArgumentException
 */
function arr_exists(array $array, string $dot, mixed $value): bool
{
    try {
        arr_first($array, $dot, $value);
        return true;
    } catch (InvalidArgumentException $e) {
        if ($e->getCode() == 2) {
            return false;
        } else {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}


/**
 * Присутствуют ли в массиве элементы, подошедшие под callback
 *
 * @param array $array
 * @param callable $callback в функцию передаются два параметра: значение и ключ.
 * @return bool
 */
function arr_some(array $array, callable $callback): bool
{
    foreach ($array as $key => $value) {

        if ($callback($value, $key)) {

            return true;
        }
    }
    return false;
}


/**
 * Вызывает callback к каждому элементу массива
 *
 * @param array $array
 * @param callable $callback
 */
function arr_each(array $array, callable $callback): void
{
    foreach ($array as $key => $value) {
        $callback($value, $key);
    }
}


/**
 * Распаковывает массив по требуемым ключам
 *
 * Если keys массив - возвращает индексный массив с элементами по ключам keys в правильном порядке
 * Если keys строка - возвращает единственное значение по указанному ключу
 *
 * @param array $assoc
 * @param string|array $keys
 * @return mixed
 * @throws InvalidArgumentException
 */
function arr_unpack(array $assoc, string|array $keys): mixed
{
    if (is_string($keys)) {
        $keys = [$keys];
        $unpack = true;
    } else {
        $unpack = false;
    }

    $result = [];

    foreach ($keys as $key) {

        if (!array_key_exists($key, $assoc)) {
            throw new InvalidArgumentException("Запрашиваемый ключ: '{$key}' не существует в массиве");
        }
        $result[] = $assoc[$key];
    }
    return $unpack ? $result[0] : $result;
}


/**
 * Извлекает из исходного массива значения по нужным ключам и возвращает индексный массив, в котором:
 *
 * 1 эл-т: исходный массив с удалёнными элементами; <br/>
 * 2 эл-т: значение из исходного массива по первому ключу; <br/>
 * 3 эл-т: значение из исходного массива по второму ключу; <br/>
 * и т.д.
 *
 *
 * @param array $assoc
 * @param string|array $keys
 * @return array
 * @throws InvalidArgumentException
 */
function arr_extract(array $assoc, string|array $keys): array
{
    if (is_string($keys)) {
        $keys = [$keys];
    }

    $result = [];

    foreach ($keys as $key) {

        if (!array_key_exists($key, $assoc)) {
            throw new InvalidArgumentException("Запрашиваемый ключ: '{$key}' не существует в массиве");
        }
        $result[] = $assoc[$key];
        unset($assoc[$key]);
    }
    return [$assoc, ...$result];
}






















/**
 * Конвертирует массив к индексному
 *
 * @param array $array
 * @return array
 */
function arr_to_list(array $array): array
{
    $result = [];
    foreach ($array as $el) {

        $result[] = $el;
    }
    return $result;
}


/**
 * Трансформирует элементы массива к типу int
 *
 * @param array $array ссылка на массив
 */
function arr_transform_to_int(array &$array): void
{
    foreach ($array as &$el) {

        if (is_numeric($el)) {

            $el = (int) $el;
        }
    }
    unset($el);
}








/**
 * Выполняет слияние ассоциативных массивов
 *
 * @param array $arrays массив с ассоциативными массива внутри
 * @param false $overwrite разрешено ли перезаписывать элементы результирующего массива
 * @return array
 * @throws LogicException
 * @throws InvalidArgumentException
 */
function arr_assoc_merge(array $arrays, $overwrite = false): array
{
    $result = [];

    foreach ($arrays as $array) {

        if (!is_array($array))     throw new InvalidArgumentException('Элемент не является массивом');
        if (!arr_is_assoc($array)) throw new InvalidArgumentException('Массив не является ассоциативным');

        foreach ($array as $key => $value) {

            if (!$overwrite && array_key_exists($key, $result)) {
                throw new LogicException("Элемент по ключу: '{$key}' уже добавлен");
            }
            $result[$key] = $value;
        }
    }
    return $result;
}


/**
 * Проверяет, присутствуют ли в массиве все указанные ключи
 *
 * @param array $array
 * @param array $keys
 * @return bool
 */
function arr_all_keys_exists(array $array, array $keys): bool
{
    foreach ($keys as $key) {

        if (!array_key_exists($key, $array)) {

            return false;
        }
    }
    return true;
}


/**
 * Возвращает массив ключей, которые отсутствуют в исходном массиве
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function arr_missing_keys(array $array, array $keys): array
{
    $result = [];

    foreach ($keys as $key) {

        if (!array_key_exists($key, $array)) {

            $result[] = $key;
        }
    }
    return $result;
}


/*
|--------------------------------------------------------------------------
| Раздел проверок содержимого массива
|--------------------------------------------------------------------------
*/


/**
 * Является ли массив ассоциативным
 *
 * @param array $array
 * @return bool
 */
function arr_is_assoc(array $array): bool
{
    foreach ($array as $key => $unused) {

        if (is_numeric($key)) {

            return false;
        }
    }
    return true;
}


/**
 * Является ли массив индексным
 *
 * @param array $array
 * @return bool
 */
function arr_is_list(array $array): bool
{
    if (empty($array)) {
        return true;
    }

    $nextKey = -1;

    foreach ($array as $key => $unused) {

        if ($key !== ++$nextKey) {

            return false;
        }
    }
    return true;
}


/**
 * Является ли массив индексным, все элементы которого
 * являются числами или строками, содержащими число
 *
 * @param array $array
 * @return bool
 */
function arr_is_numeric_list(array $array): bool
{
    if (arr_is_list($array)) {

        foreach ($array as $el) {

            if (!is_numeric($el)) {

                return false;
            }
        }
        return true;
    }
    return false;
}


/**
 * Является ли массив индексным, все элементы которого являются строками
 *
 * @param array $array
 * @return bool
 */
function arr_is_string_list(array $array): bool
{
    if (arr_is_list($array)) {

        foreach ($array as $el) {

            if (!is_string($el)) {

                return false;
            }
        }
        return true;
    }
    return false;
}


/**
 * Являются ли элементы массива экземплярами одного класса
 *
 * @param array $array
 * @param string $className
 * @return bool
 * @throws InvalidArgumentException
 */
function arr_is_generic(array $array, string $className): bool
{
    if (!class_exists($className)) {

        throw new InvalidArgumentException("Класс: '{$className}' не существует");
    }
    foreach ($array as $el) {

        if (!($el instanceof $className)) {

            return false;
        }
    }
    return true;
}


/**
 * Имеются ли повторяющиеся элементы в массиве
 *
 * @param array $array
 * @param callable|null $callback
 * @return bool
 */
function arr_has_duplicates(array $array, ?callable $callback = null): bool
{
    $callback ??= fn (mixed $value, string|int $key) => $value;

    $arr = [];

    foreach ($array as $key => $value) {

        $res = $callback($value, $key);

        if (in_array($res, $arr, true)) {
            return true;
        }
        $arr[] = $res;
    }
    return false;
}


/*
|--------------------------------------------------------------------------
| Раздел обработки html массива
|--------------------------------------------------------------------------
*/


/**
 * Возвращает html представление массива
 *
 * @param array $array
 * @return string
 */
function html_arr_encode(array $array): string
{
    return implode('#|$', $array);
}


/**
 * Декодирует строку html массива
 *
 * @param string $htmlArray
 * @return string[]
 * @throws InvalidArgumentException
 */
function html_arr_decode(string $htmlArray): array
{
    if (empty($htmlArray)) {
        return [];
    }

    $array = explode('#|$', $htmlArray);

    foreach ($array as $el) {

        if (empty($el)) {

            throw new InvalidArgumentException('html массив содержит пустой элемент');
        }
    }
    return $array;
}


/**
 * Является ли строка html массивом, все элементы которого
 * являются положительными числами
 *
 * @param string $string
 * @return bool
 */
function is_numeric_html_arr(string $string): bool
{
    return pm('/^\d+(#\|\$\d+)*$/', $string);
}


