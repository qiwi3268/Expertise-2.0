<?php


namespace App\Models\Forms\FinancingSources;

use App\Models\Traits\NumericalSnakeName;
use App\Models\AppModel;


/**
 * Представляет общие методы и настройки для моделей источников финансирования
 *
 */
abstract class FinancingSource extends AppModel
{
    use NumericalSnakeName;

    public $timestamps = false;
}