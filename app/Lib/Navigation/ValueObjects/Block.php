<?php

declare(strict_types=1);

namespace App\Lib\Navigation\ValueObjects;

use App\Lib\Singles\Roles;


final class Block
{

    /**
     * @var View[]
     */
    private array $views;


    /**
     * Конструктор класса
     *
     * @param array $schema
     */
    public function __construct(private array $schema)
    {
        self::validate($schema);

        foreach ($schema['views'] as $viewSchema) {

            $this->addView($viewSchema);
        }
    }


    /**
     * Статическая валидация схемы
     *
     * @param array $schema
     */
    public static function validate(array $schema): void
    {
        assertion()
            ->arrayHasKeys($schema, ['name', 'label', 'roles', 'views'])
            ->stringNotEmpty($schema['name'])
            ->stringNotEmpty($schema['label'])
            ->arrayNotEmpty($schema['roles'])
            ->isArray($schema['roles'])
            ->isArray($schema['views']);

        foreach ($schema['views'] as $viewSchema) {

            View::validate($viewSchema);
        }
    }


    /**
     * Добавляет представление
     *
     * @param array $viewSchema
     */
    private function addView(array $viewSchema): void
    {
        $this->views[] = new View($viewSchema);
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
     * Возвращает объект ролей
     *
     * @return Roles
     */
    public function getRoles(): Roles
    {
        return new Roles($this->schema['roles']);
    }


    /**
     * Возвращает массив представлений
     *
     * @return View[]
     */
    public function getViews(): array
    {
        return $this->views;
    }
}