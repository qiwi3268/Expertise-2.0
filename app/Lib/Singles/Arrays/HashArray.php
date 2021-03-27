<?php

declare(strict_types=1);

namespace App\Lib\Singles\Arrays;

use InvalidArgumentException;
use Traversable;


/**
 * Позволяет максимально быстро проверять наличие элементов
 */
final class HashArray
{

    /**
     * Ассоциативный bool массив
     *
     * @var bool[]
     */
    private array $data = [];


    /**
     * Конструктор класса
     *
     * @param string[] $array
     */
    public function __construct(array $array = [])
    {
        foreach ($array as $el) {

            $this->add($el);
        }
    }


    /**
     * Статический конструктор класса
     *
     * @param Traversable|array $elements
     * @param callable $callback
     * @return self
     */
    public static function createByCallback(Traversable|array $elements, callable $callback): self
    {
        $array = [];

        foreach ($elements as $key => $value) {

            $array[] = $callback($value, $key);
        }
        return new self($array);
    }


    /**
     * Добавляет элемент в хэш массив
     *
     * @param string $element
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(string $element): self
    {
        if ($this->has($element)) {
            throw new InvalidArgumentException("Элемент: '{$element}' уже добавлен в хэш массив");
        }
        if (is_numeric($element)) {
            throw new InvalidArgumentException('Добавляемый элемент не может быть числовым значением');
        }
        $this->data[$element] = true;
        return $this;
    }


    /**
     * Присутствует ли элемент в хэш массиве
     *
     * @param string $element
     * @return bool
     */
    public function has(string $element): bool
    {
        return isset($this->data[$element]);
    }


    /**
     * Отсутствует ли элемент в хэш массиве
     *
     * @param string $element
     * @return bool
     */
    public function missing(string $element): bool
    {
        return !$this->has($element);
    }


    /**
     * Возвращает массив с добавленными элементами
     *
     * @return string[]
     */
    public function getElements(): array
    {
        return array_keys($this->data);
    }
}