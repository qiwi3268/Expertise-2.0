<?php

declare(strict_types=1);

namespace App\Lib\Traits;

use Throwable;
use RuntimeException;
use LogicException;


/**
 * Делает объект идентифицируемым, добавляя свойство id
 *
 */
trait Identifiable
{

    /**
     * Идентификатор объекта
     */
    private string $id;


    /**
     * Генерирует идентификатор объекта
     *
     * @throws LogicException
     * @throws RuntimeException
     */
    private function generateId(): void
    {
        if (isset($this->id)) {
            throw new LogicException('Идентификатор объекта уже инициализирован');
        }
        try {
            $this->id = bin2hex(random_bytes(25)); // Длина 50 символов
        } catch (Throwable) {
            throw new RuntimeException("Ошибка в работе функции 'random_bytes'");
        }
    }


    /**
     * Возвращает идентификатор объекта
     *
     * @return string
     * @throws LogicException
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            throw new LogicException('Идентификатор объекта не инициализирован');
        }
        return $this->id;
    }
}