<?php

declare(strict_types=1);

namespace App\Lib\Navigation\ValueObjects;

use App\Lib\Singles\Roles;


final class View
{

    private bool $selected = false;


    /**
     * Конструктор класса
     *
     * @param array $schema
     */
    public function __construct(private array $schema)
    {
        self::validate($schema);
    }


    /**
     * Статическая валидация схемы
     *
     * @param array $schema
     */
    public static function validate(array $schema): void
    {
        assertion()
            ->arrayHasKeys($schema, ['name', 'label', 'class', 'default_roles'])
            ->stringNotEmpty($schema['name'])
            ->stringNotEmpty($schema['label'])
            //->stringClassExists($schema['class'])
            ->isArrayOrNull($schema['default_roles']);
    }


    /**
     * Устанавливает флаг того, что представление выбрано
     *
     */
    public function setSelected(): void
    {
        $this->selected = true;
    }


    /**
     * Выбрано ли представление
     *
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }


    /**
     * Возвращает свойство name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->schema['name'];
    }


    /**
     * Возвращает свойство label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->schema['label'];
    }


    /**
     * Возвращает свойство class
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->schema['class'];
    }


    /**
     * Возвращает объект ролей
     *
     * @return Roles|null
     */
    public function getDefaultRoles(): ?Roles
    {
        return is_array($this->schema['default_roles'])
            ? new Roles($this->schema['default_roles'])
            : null;
    }
}