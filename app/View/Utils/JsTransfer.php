<?php

declare(strict_types=1);

namespace App\View\Utils;


/**
 * Предназначен для трансфера переменных в js
 *
 */
final class JsTransfer
{

    private static self $instance;

    private array $storage = [];


    /**
     * Закрытый конструктор класса
     *
     */
    private function __construct() {}


    /**
     * Возвращает сущность класса
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * Устанавливает значение в хранилище
     *
     * @param string $key
     * @param mixed $value
     */
    public function put(string $key, mixed $value): void
    {
        $this->storage[$key] = $value;
    }


    /**
     * Предназначен для преобразования к строке
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->storage);
    }
}
