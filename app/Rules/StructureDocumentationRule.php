<?php


namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Singles\TypeOfObjectBridge;
use App\Repositories\StructureDocumentation\StructureDocumentationRepository;


/**
 * Правила валидации структуры документации
 *
 */
final class StructureDocumentationRule implements Rule
{

    private StructureDocumentationRepository $rep;


    /**
     * Конструктор класса
     *
     * @param string $snakeMappings
     */
    public function __construct(string $snakeMappings)
    {
        $this->rep = TypeOfObjectBridge::createByDocumentationMappings($snakeMappings)
            ->getStructureDocumentationRepository();
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param int $id
     * @return bool
     */
    public function passes($attribute, $id): bool
    {
        return $this->rep->existsById($id);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return 'Полученный узел структуры документации не существует в БД';
    }
}
