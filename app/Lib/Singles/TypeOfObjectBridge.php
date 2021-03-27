<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;

use App\Models\StructureDocumentation\StructureDocumentation;
use App\Models\StructureDocumentation\StructureDocumentation1;
use App\Models\StructureDocumentation\StructureDocumentation2;

use App\Repositories\StructureDocumentation\StructureDocumentationRepository;
use App\Repositories\StructureDocumentation\StructureDocumentation1Repository;
use App\Repositories\StructureDocumentation\StructureDocumentation2Repository;

use App\Lib\Settings\FileMappingsManager;


/**
 * Обслуживает разветвления, связанные с видом объекта
 *
 */
final class TypeOfObjectBridge
{

    /**
     * 1 - производственные/непроизводственные
     * 2 - линейные
     */
    private int $type;


    /**
     * Конструктор класса
     *
     * @param int $type вид объекта формата
     * @throws InvalidArgumentException
     */
    public function __construct(int $type)
    {
        if ($type != 1 && $type != 2) {
            throw new InvalidArgumentException("Некорректный вид объекта: '{$type}'");
        }
        $this->type = $type;
    }


    /**
     * Статический конструктор класса
     *
     * @param string $snakeMappings
     * @return self
     * @throws InvalidArgumentException
     */
    public static function createByDocumentationMappings(string $snakeMappings): self
    {
        $mgr = new FileMappingsManager($snakeMappings);

        if ($snakeMappings != '1_2_1' && $snakeMappings != '1_2_2') {
            throw new InvalidArgumentException("Указанные snakeMappings: '{$snakeMappings}' не соответствуют документации");
        }
        [,,$type] = $mgr->getMappings();
        return new self($type);
    }


    /**
     * Возвращает экземпляр модели структуры документации
     *
     * @return StructureDocumentation
     */
    public function getStructureDocumentationModel(): StructureDocumentation
    {
        if ($this->type == 1) {
            return new StructureDocumentation1;
        } else {
            return new StructureDocumentation2;
        }
    }


    /**
     * Возвращает экземпляр репозитория структуры документации
     *
     * @return StructureDocumentationRepository
     */
    public function getStructureDocumentationRepository(): StructureDocumentationRepository
    {
        if ($this->type == 1) {
            return new StructureDocumentation1Repository;
        } else {
            return new StructureDocumentation2Repository;
        }
    }
}