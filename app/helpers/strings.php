<?php

declare(strict_types=1);


/**
 * Определяет, содержит ли строка все подстроки
 *
 * @param string $haystack строка, в которой производится поиск
 * @param string[] $needles подстроки
 * @return bool
 */
function str_contains_all(string $haystack , array $needles): bool
{
    foreach ($needles as $needle) {

        if (!str_contains($haystack, $needle)) {

            return false;
        }
    }
    return true;
}


/**
 * Определяет, содержит ли строка хоть одну подстроку
 *
 * @param string $haystack строка, в которой производится поиск
 * @param string[] $needles подстроки
 * @return bool
 */
function str_contains_any(string $haystack , array $needles): bool
{
    foreach ($needles as $needle) {

        if (str_contains($haystack, $needle)) {

            return true;
        }
    }
    return false;
}


/**
 * Возвращает массив подстрок, которые не содержатся в строке
 *
 * @param string $haystack строка, в которой производится поиск
 * @param string[] $needles подстроки
 * @return array
 */
function str_get_missing(string $haystack, array $needles): array
{
    $result = [];

    foreach ($needles as $needle) {

        if (!str_contains($haystack, $needle)) {

            $result[] = $needle;
        }
    }
    return $result;
}







