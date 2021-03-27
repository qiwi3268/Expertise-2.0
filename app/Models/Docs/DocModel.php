<?php


namespace App\Models\Docs;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AppModel;


/**
 * Представляет общие методы и настройки для моделей документов
 *
 */
abstract class DocModel extends AppModel
{
    use SoftDeletes;
}
