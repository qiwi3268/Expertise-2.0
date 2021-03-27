<?php


namespace App\Models\Forms\Applicants;

use App\Models\Traits\NumericalSnakeName;
use App\Models\AppModel;


/**
 * Представляет общие методы и настройки для моделей заявителей
 *
 */
abstract class Applicant extends AppModel
{
    use NumericalSnakeName;

    public $timestamps = false;
}