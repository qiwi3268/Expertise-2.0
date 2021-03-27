<?php

declare(strict_types=1);

namespace App\Lib\Settings;


final class NavigationManager
{
    private const YML_PATH = 'navigation';

    private static self $instance;

    private array $schema;


    /**
     * Закрытый конструктор класса
     *
     */
    private function __construct()
    {
        $this->schema = yml(self::YML_PATH)['blocks'];
    }


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
     * Возвращает схему
     *
     * @return array
     */
    public function getSchema(): array
    {
        return $this->schema;
    }
}
