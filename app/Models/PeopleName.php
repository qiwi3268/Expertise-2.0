<?php

declare(strict_types=1);

namespace App\Models;


/**
 * Имена для парсинга ЭЦП
 *
 */
final class PeopleName extends AppModel
{
    protected $primaryKey = 'name';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
