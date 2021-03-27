<?php

declare(strict_types=1);

namespace App\View\Utils;

use App\ApiServices\FileUploading\FileUploader;
use App\Lib\Settings\FileMappingsManager;


/**
 * Содержит вспомогательные функции для работы с файлами
 *
 */
final class FilesHelper
{

    /**
     * Возвращает массив правил
     *
     * @param string $snakeMappings
     * @return array
     */
    public static function getRules(string $snakeMappings): array
    {
        $mgr = new FileMappingsManager($snakeMappings);

        /** @var FileUploader $className */
        $className = $mgr->getUploaderClassName();

        $lib = $className::getFileRulesLibrary();

        $a = $lib->getMaxFileSize();
        $b = $lib->getAllowableExtensions();
        $c = $lib->getForbiddenSymbols();

        return [
            !is_null($a) ? (string) $a         : '',
            !is_null($b) ? html_arr_encode($b) : '',
            !is_null($c) ? html_arr_encode($c) : ''
        ];
    }
}
