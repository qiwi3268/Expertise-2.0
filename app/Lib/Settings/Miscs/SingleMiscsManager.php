<?php

declare(strict_types=1);

namespace App\Lib\Settings\Miscs;


/**
 * Менеджер файла miscs, раздела one_to_many_miscs
 *
 */
final class SingleMiscsManager
{
    private const YML_PATH = 'miscs';

    private static self $instance;

    private array $schema;


    /**
     * Закрытый конструктор класса
     *
     */
    private function __construct()
    {
        $this->schema = yml(self::YML_PATH)['single_miscs'];
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
     * Проверяет существование блока по алиасу
     *
     * @param string $alias
     * @return bool
     */
    public function existsByAlias(string $alias): bool
    {
        return arr_exists($this->schema, 'alias', $alias);
    }


    /**
     * Возвращает полное названия класса по его алиасу
     *
     * @param string $alias
     * @return string
     */
    public function getClassNameByAlias(string $alias): string
    {
        return arr_first($this->schema, 'alias', $alias, 'class');
    }
}
