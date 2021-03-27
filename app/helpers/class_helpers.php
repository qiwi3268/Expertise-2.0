<?php

declare(strict_types=1);

use App\Lib\Settings\DocumentsManager;
use App\Lib\Settings\YamlSettingsInitializer;
use App\View\Utils\JsTransfer;
use App\Lib\Assertions\Assert;
use Carbon\CarbonImmutable;


/**
 * Возвращает экземпляр менеджера документов
 *
 * @return DocumentsManager
 */
function doc(): DocumentsManager
{
    return DocumentsManager::getInstance();
}


/**
 * Устанавливает значение в хранилище трансфера переменных в js
 *
 * @param string $key
 * @param mixed $value
 * @return void
 */
function jst(string $key, mixed $value): void
{
    JsTransfer::getInstance()->put($key, $value);
}


/**
 * Возвращает данные yml файла
 *
 * @param string $path путь к yml файлу от директории settings
 * @return array
 */
function yml(string $path): array
{
    return (new YamlSettingsInitializer($path))->get();
}


/**
 * Возвращает смещенную дату на указанное количество рабочих дней
 *
 * @param int $offset количество дней смещения
 * @return CarbonImmutable
 */
function dater(int $offset): CarbonImmutable
{
    return CarbonImmutable::now()->shiftBusinessDays($offset);
}


/**
 * Возвращает экземпляр утверждений
 *
 * @param string|null $prefix
 * @return Assert
 */
function assertion(?string $prefix = null): Assert
{
    return new Assert($prefix);
}
