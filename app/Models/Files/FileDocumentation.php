<?php


namespace App\Models\Files;

use App\Models\AppModel;


/**
 * Файлы документации
 *
 */
final class FileDocumentation extends AppModel
{
    public $timestamps = false;

    protected $fillable = [
        'structure_id',
        'structure_type',
        'file_id'
    ];
}
