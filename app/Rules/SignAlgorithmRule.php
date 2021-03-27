<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Csp\AlgorithmsManager;


/**
 * Правила валидации алгоритма подписи
 *
 */
final class SignAlgorithmRule implements Rule
{

    /**
     * Конструктор класса
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param mixed $alg
     * @return bool
     */
    public function passes($attribute, $alg): bool
    {
        return AlgorithmsManager::existSignAlgorithm($alg);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return 'Полученный алгоритм подписи некорректен';
    }
}
