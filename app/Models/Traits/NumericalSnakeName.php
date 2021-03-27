<?php


namespace App\Models\Traits;
use Illuminate\Support\Str;


/**
 * Преобразовывает имя таблицы к numeric snake нотации
 *
 */
trait NumericalSnakeName
{

    /**
     *
     */
    public function initializeNumericalSnakeName(): void
    {
        $this->setTable((string) Str::numericSnake(class_basename($this)));
    }
}
