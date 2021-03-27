<?php

declare(strict_types=1);

namespace App\Lib\Assertions;

use App\Exceptions\Assertions\AssertException;



final class Assert
{
    private string $exception = AssertException::class;


    /**
     * Конструктор класса
     *
     * @param string|null $prefix префикс к сообщению исключения
     */
    public function __construct(private ?string $prefix = null)
    {
        $this->stringClassExists($this->exception);
    }


    /**
     * Выбрасывает исключение
     *
     * @param string $message
     * @throws AssertException
     */
    private function throw(string $message): void
    {
        if (is_string($p = $this->prefix)) {
            $message = "{$p}. {$message}";
        }
        throw new $this->exception($message);
    }


    /**
     * Возвращает тип переменной
     *
     * @param mixed $value
     * @return string
     */
    private function getType(mixed $value): string
    {
        if (true === $value) {
            return 'true (bool)';
        }
        if (false === $value) {
            return 'false (bool)';
        }
        return get_value_types($value)[0];
    }



    /*
    |--------------------------------------------------------------------------
    | Раздел проверки типов
    |--------------------------------------------------------------------------
    */


    /**
     * Массив
     *
     * @param mixed $value
     * @return $this
     * @throws AssertException
     */
    public function isArray(mixed $value): self
    {
        if (!is_array($value)) {

            $this->throw("Ожидался тип array. Получен {$this->getType($value)}");
        }
        return $this;
    }


    /**
     * Массив или null
     *
     * @param mixed $value
     * @return $this
     * @throws AssertException
     */
    public function isArrayOrNull(mixed $value): self
    {
        if (!is_array($value) && !is_null($value)) {

            $this->throw("Ожидался тип array или null. Получен {$this->getType($value)}");
        }
        return $this;
    }


    /**
     * Строка
     *
     * @param mixed $value
     * @return $this
     * @throws AssertException
     */
    public function isString(mixed $value): self
    {
        if (!is_string($value)) {

            $this->throw("Ожидался тип string. Получен {$this->getType($value)}");
        }
        return $this;
    }


    /**
     * Существующий файл в ФС сервера
     *
     * @param string $path абсолютный путь к файлу в ФС сервера
     * @return $this
     * @throws AssertException
     */
    public function isFile(string $path): self
    {
        if (!is_file($path)) {

            $this->throw("Ожидался существующий файл по пути: '{$path}'");
        }
        return $this;
    }



    /*
    |--------------------------------------------------------------------------
    | Раздел обработки массивов
    |--------------------------------------------------------------------------
    */


    /**
     * Непустой массив
     *
     * @param mixed $value
     * @return $this
     * @throws AssertException
     */
    public function arrayNotEmpty(mixed $value): self
    {
        $this->isArray($value);

        if ($value === []) {

            $this->throw('Ожидался непустой массив');
        }
        return $this;
    }



    /**
     * Индексный строковый массив
     *
     * @param mixed $array
     * @return $this
     * @throws AssertException
     */
    public function arrayIsStringList(mixed $array): self
    {
        $this->isArray($array);

        if (!arr_is_string_list($array)) {

            $this->throw('Ожидался индексный строковый массив');
        }
        return $this;
    }


    /**
     * Индексный строковый массив без повторяющихся элементов
     *
     * @param mixed $array
     * @return $this
     * @throws AssertException
     */
    public function arrayIsStringListWithoutDuplicates(mixed $array): self
    {
        $this->arrayIsStringList($array);

        if (arr_has_duplicates($array)) {

            $this->throw('Ожидался индексный строковый массив без повторяющихся элементов');
        }
        return $this;
    }


    /**
     * Массив с нужными ключами
     *
     * @param mixed $array
     * @param array $keys
     * @return $this
     */
    public function arrayHasKeys(mixed $array, array $keys): self
    {
        $this->isArray($array);

        [$err, $debug] = info_implode(arr_missing_keys($array, $keys));

        if ($err) {

            $this->throw("Ожидался массив, в котором присутствуют ключи: '{$debug}'");
        }
        return $this;
    }




    /*
    |--------------------------------------------------------------------------
    | Раздел обработки строк
    |--------------------------------------------------------------------------
    */



    /**
     * Непустая строка
     *
     * @param mixed $value
     * @return $this
     * @throws AssertException
     */
    public function stringNotEmpty(mixed $value): self
    {
        $this->isString($value);

        if ($value === '') {

            $this->throw('Ожидалась непустая строка');
        }
        return $this;
    }


    /**
     * Строка, равная ожидаемой
     *
     * @param mixed $value
     * @param string $expect ожидаемая строка
     * @return $this
     * @throws AssertException
     */
    public function stringEquals(mixed $value, string $expect): self
    {
        $this->isString($value);

        if ($value !== $expect) {

            $this->throw("Ожидалась строка, равная: '{$expect}'. Получено: '{$value}'");
        }
        return $this;
    }


    /**
     * Строка с именем существующего класса
     *
     * @param mixed $value
     * @return $this
     * @throws AssertException
     */
    public function stringClassExists(mixed $value): self
    {
        $this->isString($value);

        if (!class_exists($value)) {

            $this->throw("Ожидалась строка с именем существующего класса. Получено: '{$value}'");
        }
        return $this;
    }



    /*
    |--------------------------------------------------------------------------
    | Раздел обработки объектов
    |--------------------------------------------------------------------------
    */





    /**
     * НЕ ПРОВЕРЕНО
     *
     * Проверяет объект на то, что он является экземляром/реализует интерфейс
     *
     * @param string|object $value
     * @param string|object $class
     */
   /* public function instanceof(string|object $value, string|object $class): void
    {
        if (!($value instanceof $class)) {
            //todo проверить с строками
            $this->throw("Ожидалось, что класс: '{$this->getClassName($value)}' является экземпляром класса: '{$this->getClassName($class)}'");
        }
    }*/

}