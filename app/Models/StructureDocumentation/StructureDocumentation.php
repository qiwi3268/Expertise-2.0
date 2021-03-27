<?php


namespace App\Models\StructureDocumentation;

use App\Models\AppModel;
use App\Models\Traits\NumericalSnakeName;


/**
 * Представляет общие методы и настройки для моделей структуры документации
 *
 */
abstract class StructureDocumentation extends AppModel
{
    use NumericalSnakeName;

    protected $casts = [
        'is_header' => 'bool',
        'is_active' => 'bool',
    ];
}