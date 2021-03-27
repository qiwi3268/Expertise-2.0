<?php

declare(strict_types=1);

namespace App\Lib\Csp;

use InvalidArgumentException;


/**
 * Предназначен для работы с алгоритмами подписи и хэширования
 *
 */
final class AlgorithmsManager
{

    /**
     * Алгоритмы подписи
     *
     */
    private const SIGN_ALGORITHMS = [
        '1.2.643.2.2.19'    => '1.2.643.2.2.19',    // Алгоритм ГОСТ Р 34.10-2001, используемый при экспорте/импорте ключей
        '1.2.643.7.1.1.1.1' => '1.2.643.7.1.1.1.1', // Алгоритм ГОСТ Р 34.10-2012 для ключей длины 256 бит, используемый при экспорте/импорте ключей
        '1.2.643.7.1.1.1.2' => '1.2.643.7.1.1.1.2', // Алгоритм ГОСТ Р 34.10-2012 для ключей длины 512 бит, используемый при экспорте/импорте ключей
    ];


    /**
     * Алгоритмы хэширования
     *
     * Соответсвие алгоритмов хэширования к алгоритмам подписи
     *
     */
    private const HASH_ALGORITHMS = [
        self::SIGN_ALGORITHMS['1.2.643.2.2.19']    => '1.2.643.2.2.9',     // Функция хэширования ГОСТ Р 34.11-94
        self::SIGN_ALGORITHMS['1.2.643.7.1.1.1.1'] => '1.2.643.7.1.1.2.2', // Функция хэширования ГОСТ Р 34.11-2012, длина выхода 256 бит
        self::SIGN_ALGORITHMS['1.2.643.7.1.1.1.2'] => '1.2.643.7.1.1.2.3'  // Функция хэширования ГОСТ Р 34.11-2012, длина выхода 512 бит
    ];


    /**
     * Проверяет существование алгоритма подписи
     *
     * @param string $alg
     * @return bool
     */
    public static function existSignAlgorithm(string $alg): bool
    {
        return isset(self::SIGN_ALGORITHMS[$alg]);
    }


    /**
     * Возвращает алгоритм хэширования, который соответствует алгоритму подписи
     *
     * @param string $signAlg
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getHashBySignAlgorithm(string $signAlg): string
    {
        if (!self::existSignAlgorithm($signAlg)) {
            throw new InvalidArgumentException("Указан несуществующий алгоритм подписи: '{$signAlg}'");
        }
        return self::HASH_ALGORITHMS[$signAlg];
    }
}
