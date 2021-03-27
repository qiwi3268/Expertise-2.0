<?php

declare(strict_types=1);

namespace App\Lib\Cache;

use InvalidArgumentException;


/**
 * Кэш хранилище в виде массива
 */
final class CacheArray
{

    private array $data = [];


    /**
     * Конструктор класса
     *
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        foreach ($array as $key => $value) {
            $this->put($key, $value);
        }
    }


    /**
     * Добавляет элемент
     *
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function put(string $key, mixed $value): void
    {
        if ($this->has($key)) {
            throw new InvalidArgumentException("Элемент по ключу: '{$key}' уже добавлен в кэш хранилище");
        }
        if (is_numeric($key)) {
            throw new InvalidArgumentException("Ключ элемента в кэш хранилище не может быть числовым значением: {$key}");
        }
        $this->data[$key] = $value;
    }


    /**
     * Возвращает элемент
     *
     * @param string $key
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException("Элемент по ключу: '{$key}' не добавлен в кэш хранилище");
        }
        return $this->data[$key];
    }


    /**
     * Возвращает элемент, если он присутствует в хранилище
     *
     * Если элемент отсутствует, то добавляет его со значением, которое вернула callback функция,
     * затем возвращает это значение
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function remember(string $key, callable $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value);
        return $value;
    }


    /**
     * Проверяет наличие элемента
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
}