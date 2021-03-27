<?php


namespace App\Models\Application;
use App\Models\AppModel;


/**
 * Счетчики заявлений
 *
 */
final class ApplicationCounter extends AppModel
{
    protected $fillable = [
        'year',
        'counter',
        'doc_application_id'
    ];
}
