<?php


namespace App\Models\Sys;

use App\Models\AppModel;


/**
 * Системные данные файловых маппингов
 *
 */
final class SysFileMappings extends AppModel
{
    protected $table = 'sys_file_mappings';
    public $timestamps = false;
}
