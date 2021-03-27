<?php

declare(strict_types=1);

namespace App\Repositories;

use LogicException;
use InvalidArgumentException;
use App\Exceptions\Repositories\ResultDoesNotExistException;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\AppModel;
use App\Models\Utils\RelationHandler;


/**
 * Корневой класс всех репозиториев
 *
 * @method bool existsById(int $id)
 */
abstract class Repository
{

    private AppModel $model;
    private string $table;


    /**
     * Конструктор класса
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function __construct()
    {
        if (!isset($this->modelClassName)) {
            throw new LogicException("Свойство 'modelClassName' не инициализировано");
        }
        if (!class_exists($this->modelClassName)) {
            throw new InvalidArgumentException("Класс модели: '{$this->modelClassName}' не существует");
        }

        $this->model = new $this->modelClassName;
        $this->table = $this->model->getTable();
    }


    /**
     * Возвращает новый экземпляр модели
     *
     * @return AppModel
     */
    protected function m(): AppModel
    {
        return clone $this->model;
    }


    /**
     * Возвращает построитель запросов от фасада DB
     *
     * @return Builder
     */
    protected function getDataBaseBuilder(): Builder
    {
        return DB::table($this->table);
    }


    /**
     * Предназначен для магического построения запроса на основе наименования метода
     *
     * Функционал представлен в двух реализациях:
     * 1 - метод getColumns, например, getIdByLevel1AndLevel2
     * 2 - метод exists, например, existsByLevel1AndLevel2
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ResultDoesNotExistException
     */
    public function __call(string $name , array $arguments): mixed
    {
        if (pm('/^get(.+)By(.+)$/', $name, $m)) {
            return $this->handleGetMagic($m[0], $m[1], $arguments);
        }
        if (pm('/^existsBy(.+)$/', $name, $m)) {
            return $this->handleExistsMagic($m, $arguments);
        }
        throw new InvalidArgumentException('Неверный формат магического вызова');
    }


    /**
     * Внутренний алгоритм для обработки методов типа:
     * getIdByLevel1AndLevel2
     * getLevel1AndLevel2ById
     *
     * @param string $columnsPart
     * @param string $filterPart
     * @param array $arguments первые обязательные параметры для построения whereArray.
     * Последний параметр (кол-во обязательных + 1) bool значение checkIsset
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ResultDoesNotExistException
     */
    private function handleGetMagic(string $columnsPart, string $filterPart, array $arguments): mixed
    {
        $columns = Str::of($columnsPart)
            ->explode('And')
            ->transform(fn (string $item) => Str::numericSnake($item));

        $columns = ($columns->count() == 1) ? (string) $columns->get(0) : $columns->toArray();

        $filter = Str::of($filterPart)
            ->explode('And')
            ->transform(fn (string $item) => Str::numericSnake($item));

        $argumentsCount = count($arguments);
        $filterCount = $filter->count();

        if ($argumentsCount == ($filterCount + 1)) {
            $checkIsset = (bool) array_pop($arguments);
        } elseif ($argumentsCount == $filterCount) {
            $checkIsset = true;
        } else {
            throw new InvalidArgumentException('Некорректное количество входных параметров');
        }

        $whereArray = [];

        for ($l = 0; $l < $filterCount; $l++) {
            $whereArray[(string) $filter->get($l)] = $arguments[$l];
        }
        return $this->getColumns($columns, $whereArray, $checkIsset);
    }


    /**
     * Внутренний алгоритм для обработки методов типа:
     * existsByLevel1AndLevel2
     *
     * @param string $filterPart
     * @param array $arguments
     * @return bool
     * @throws InvalidArgumentException
     */
    private function handleExistsMagic(string $filterPart, array $arguments): bool
    {
        $filter = Str::of($filterPart)
            ->explode('And')
            ->transform(fn (string $item) => Str::numericSnake($item));

        $filterCount = $filter->count();

        if (count($arguments) != $filterCount) {
            throw new InvalidArgumentException('Некорректное количество входных параметров');
        }

        $whereArray = [];

        for ($l = 0; $l < $filterCount; $l++) {
            $whereArray[(string) $filter->get($l)] = $arguments[$l];
        }
        return $this->exists($whereArray);
    }


    /**
     * whereArray массив формата:
     *
     * [
     *      ['col1', '>', 'val1'],
     *      ['col2', 'val2']
     * ]
     *
     * ИЛИ
     *
     * ['col1' => 'val1',
     *  'col2' => 'val2']
     * Данный массив преобразуется к первому из этого примера со знаками равенства
     */


    /**
     * Проверяет существование записи
     *
     * @param array $whereArray
     * @return bool
     */
    public function exists(array $whereArray): bool
    {
        $this->handleWhereArray($whereArray);

        return $this->getDataBaseBuilder()->where($whereArray)->exists();
    }


    /**
     * Возвращает значения нужных столбцов
     *
     * @param array|string $columns
     * @param array $whereArray
     * @param bool $checkIsset
     * @return mixed
     * @throws ResultDoesNotExistException
     */
    public function getColumns(array|string $columns, array $whereArray, bool $checkIsset = true): mixed
    {
        $this->handleWhereArray($whereArray);

        $r = $this->getDataBaseBuilder()->where($whereArray)->first($columns);

        try {
            $this->checkIssetResult($r);
        } catch (ResultDoesNotExistException $e) {
            return $checkIsset ? throw $e : null;
        }

        if (is_array($columns)) {
            $result = [];
            foreach ($columns as $column) $result[] = $r->{$column};
        } else {
            $result = $r->{$columns};
        }
        return $result;
    }


    /**
     * Возвращает id записи
     *
     * Сокращенная форма вызова метода getColumns
     *
     * @param array $whereArray
     * @param bool $checkIsset
     * @return int
     * @throws ResultDoesNotExistException
     */
    public function getId(array $whereArray, bool $checkIsset = true): int
    {
        return $this->getColumns('id', $whereArray, $checkIsset);
    }


    /**
     * Проверяет возвращенный результат на существование (не null)
     *
     * @param mixed $param
     * @return mixed
     * @throws ResultDoesNotExistException
     */
    protected function checkIssetResult(mixed $param): mixed
    {
        return $param ?? throw new ResultDoesNotExistException('Запрос к БД вернул пустой результат');
    }


    /**
     * Обрабатывает where массив
     *
     * Ассоциативный массив будет предобразован к индексному нужного формата для запроса
     *
     * @param array $array
     */
    private function handleWhereArray(array &$array): void
    {
        // Упрощенная проверка на ассоциативный массив
        if (is_string(array_key_first($array))) {
            $a = [];
            foreach ($array as $key => $value) $a[] = [$key, '=', $value];
            $array = $a;
        }
    }


    /**
     * Проверяет существование записи из pivot таблицы по отношению BelongsToMany
     *
     * @param AppModel $model
     * @param string $relationName название метода
     * @param int $foreignId id элемента модели
     * @param int $relatedId id зависимого элемента
     * @return bool
     */
    public function existsBelongsToManyRelation(
        AppModel $model,
        string $relationName,
        int $foreignId,
        int $relatedId,
    ): bool {

        $obj = RelationHandler::getBelongsToManyProperties($model, $relationName);

        return DB::table($obj->table)->where([
            $obj->foreign => $foreignId,
            $obj->related => $relatedId
        ])->exists();
    }
}
