<?php

declare(strict_types=1);

namespace App\Lib\Navigation\Views;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;


abstract class ViewHandler
{
    private Builder $builder;


    /**
     * Конструктор класса
     *
     */
    public function __construct()
    {
        $this->builder = $this->getFilterBuilder();
    }


    // todo где накладывать дополнительную фильтрацию..?ы

    /**
     * Возвращает количество записей
     *
     * @return int
     */
    public function getCount(): int
    {
        //todo что дают параметры в методе count?
        return $this->builder->count();
    }


    /**
     * Возвращает построитель запросов, который содержит только фильтрующую логику
     *
     * Объект не должен включать в себя загрузку отношений и т.д.
     *
     * @return Builder
     */
    abstract protected function getFilterBuilder(): Builder;


    /**
     * Возвращает объект View, включайщий в себя нужные данные
     *
     */
    abstract protected function getView(): View;
}