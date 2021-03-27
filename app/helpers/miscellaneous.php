<?php

declare(strict_types=1);

use App\Exceptions\EnvException;
use App\Exceptions\ConfigException;

use App\Models\UsersData\User;


/**
 * Обёртка для работы с функцией preg_match
 *
 * Возвращает требуемые вхождения в виде массива/строки или null
 *
 * @param string $pattern
 * @param string $subject
 * @param string[]|string|null $matches
 * Массив - если групп больше одной. Если * группа не найдена - значение элемента будет false.<br/>
 * Строка - если группа одна. Или если групп нет, то полное вхождение шаблона.<br/>
 * null - если вхождений не найдено
 * @return bool true, если было любое вхождение (matches не null)
 * @throws InvalidArgumentException
 */
function pm(string $pattern, string $subject, array|string|null &$matches = null): bool
{
    if (@preg_match($pattern, $subject, $m, PREG_UNMATCHED_AS_NULL) === false) {
        $msg = preg_last_error_msg();
        $code = preg_last_error();
        throw new InvalidArgumentException("Ошибка в работе функции preg_match. Message: '{$msg}'. Code: {$code}");
    }

    foreach ($m as &$item) $item ??= false;

    $matchesCount = count($m);

    $matches = match ($matchesCount) {
        0 => null,
        1 => $m[0], // Полное вхождение шаблона
        2 => $m[1], // Первая и единственная группа
        default => []
    };

    if ($matches === []) {

        for ($f = 1; $f < $matchesCount; $f++) {
            $matches[] = $m[$f];
        }
    }
    return !is_null($matches);
}


/**
 * Объединяет элементы непустого массива в строку
 *
 * @param array $array
 * @param string $separator
 * @return array [bool, string|null]<br/>
 * 1 эл-т - существуют ли элементы в массиве<br/>
 * 2 эл-т - объединенная строка, если в массиве существуют элементы. В противном случае - null
 */
function info_implode(array $array, string $separator = ', '): array
{
    $has = !empty($array);

    return [
        $has,
        $has ? implode($separator, $array) : null
    ];
}


/**
 * Объединяет элементы ассоциативного массива в строку
 *
 * @param array $array ассоциативный массив
 * @param string $keySeparator разделитель ключа и значения
 * @param string $partsSeparator разделитель между частями
 * @return string
 */
function assoc_implode(array $array, string $keySeparator = '=', string $partsSeparator = ', '): string
{
    $arr = [];

    foreach ($array as $key => $value) {

        $arr[] = "{$key}{$keySeparator}{$value}";
    }
    return implode($partsSeparator, $arr);
}


/**
 * Возвращает значение обязательной переменной окружения
 *
 * @param string $key
 * @param bool $convertNumericType требуется ли преобразование к int,
 * если значение является строкой, содержащей число
 * @return mixed
 * @return EnvException
 */
function required_env(string $key, bool $convertNumericType = false): mixed
{
    $error = '8214df91-9352-4b98-92d9-6dcde893236e'; // Псевдо-уникальное значение

    $result = env($key, $error);

    if ($result === $error) {
        throw new EnvException("Переменная окружения '{$key}' не задана");
    }
    if ($convertNumericType && is_numeric($result)) {
        return (int) $result;
    }
    return $result;
}


/**
 * Возвращает значение обязательной переменной файла конфигурации
 *
 * @param string $key
 * @return mixed
 * @throws ConfigException
 */
function required_config(string $key): mixed
{
    $error = '8214df91-9352-4b98-92d9-6dcde893236e'; // Псевдо-уникальное значение

    $result = config($key, $error);

    if ($result === $error) {
        throw new ConfigException("Переменная конфигурации '{$key}' не задана");
    }
    return $result;
}



/**
 * Возвращает массив типов переменной
 *
 * @param mixed $value
 * @return string[]
 */
function get_value_types(mixed $value): array
{
    if (!is_object($value)) {
        return [get_debug_type($value)];
    }

    return [
        $value::class,
        ...array_values(class_parents($value)),
        ...array_values(class_implements($value)),
    ];
}


/**
 * Возвращает модель пользователя, если он аутентифицирован.
 * False в противном случае
 *
 * @param bool $mustBeAuth требуется ли выбрасывать исключение, если аутентификация отсутствует
 * @return bool|User
 * @throws DomainException
 */
function auth_user(bool $mustBeAuth = true): bool|User
{
    $auth = auth();

    if(!$auth->check()) {

        if ($mustBeAuth) {

            throw new DomainException('Отсутствует аутентификация пользователя');
        }
        return false;
    }

    /** @var User $user */
    $user = $auth->user();

    return $user;
}


