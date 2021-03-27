<?php

declare(strict_types=1);

namespace App\Lib\Stream;

use InvalidArgumentException;


/**
 * Предназначен для правильной разбивки и обработки потоковых данных на части
 *
 */
final class StreamPartsMaker
{

    /**
     * Сформированные части потока, разделенные символом переноса строки
     * и обработанные клиентским хэндлером
     *
     */
    private array $parts = [];

    /**
     * Хвост
     *
     * Последняя часть разбитого чанка
     *
     */
    private string $tail = '';

    /**
     * Клиентский хэндлер сформированной части
     *
     */
    private mixed $handler;


    /**
     * Конструктор класса
     *
     * @param callable $handler клиентский хэндер, в компетенцию которого
     * входит обработка сформированной части:
     * фильтрация/разбивка её на несколько частей/модификация
     * @param string $separator разделитель
     */
    public function __construct(
        callable $handler,
        private $separator = PHP_EOL
    ) {
        $this->handler = $handler;
    }


    /**
     * Выполняет обработку
     *
     * @param string $type тип потока вывода не используется
     * @param string $chunk чанк потока
     */
    public function processChunk(string $type, string $chunk): void
    {
        $chunkParts = explode($this->separator, $chunk);
        $lastChunkPart = array_pop($chunkParts);

        if (empty($chunkParts)) {

            // Разделитель отсутствует
            $this->tail .= $lastChunkPart;
        } else {

            // К хвосту добавляем первый элемент массива
            $this->processPart($this->tail . array_shift($chunkParts));
            // Все промежуточные части чанка являются сформированными частями
            foreach ($chunkParts as $part) $this->processPart($part);
            // Последняя часть чанка становится хвостом
            $this->tail = $lastChunkPart;
        }
    }


    /**
     * Выполняет обработку сформированной части и добавления к результирующему массиву
     *
     * @param string $part
     * @throws InvalidArgumentException
     */
    private function processPart(string $part): void
    {
        $handled = call_user_func($this->handler, $part);

        if (!is_array($handled)) {
            throw new InvalidArgumentException("Результатом вызова клиентского хэндлера должен быть массив");
        }

        if (!empty($handled)) {
            $this->parts = [...$this->parts, ...$handled];
        }
    }


    /**
     * Возвращает итоговый результат
     *
     * @return array
     */
    public function getParts(): array
    {
        // Добавление оставшегося хвоста к результату
        $this->processPart($this->tail);
        $this->tail = '';
        return $this->parts;
    }
}
