<?php


namespace App\Lib\Settings\Miscs;

use App\Exceptions\Lib\Settings\SettingsLogicException;
use stdClass;


/**
 * Менеджер файла miscs, раздела dependent_miscs
 *
 */
final class DependentMiscsManager
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
        $this->schema = yml(self::YML_PATH)['dependent_miscs'];
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
     * Проверяет существование блока по алиасам главного и зависимого справочника
     *
     * @param string $mainAlias
     * @param string $subAlias
     * @return bool
     */
    public function existsByAliases(string $mainAlias, string $subAlias): bool
    {
        foreach ($this->schema as $block) {

            if (
                $block['main']['alias'] == $mainAlias
                && $block['sub']['alias'] == $subAlias
            ) {
                return true;
            }
        }
        return false;
    }


    /**
     * Возвращает объект блока по алиасам главного и зависимого справочника
     *
     * @param string $mainAlias
     * @param string $subAlias
     * @return stdClass
     * <pre>
     * Object (
     *     main [
     *         class    -> string,
     *         alias    -> string,
     *         relation -> string
     *     ],
     *     sub [
     *         class    -> string,
     *         alias    -> string,
     *         relation -> string
     *     ]
     * )
     * </pre>
     * @throws SettingsLogicException
     */
    public function getObjectByAliases(string $mainAlias, string $subAlias): stdClass
    {
        foreach ($this->schema as $block) {

            if (
                $block['main']['alias'] == $mainAlias
                && $block['sub']['alias'] == $subAlias
            ) {
                return (object) $block;
            }
        }
        throw new SettingsLogicException("Не найдено данных по алиасам справочников: '{$mainAlias}', '{$subAlias}'");
    }
}
