<?php


namespace App\Exceptions\Lib\Settings;

use LogicException;


/**
 * Связан с ошибками при работе пакета менеджеров yml файлов
 *
 * Характеризует ошибки при попытке обращения к несуществующим узлам
 * и другим ошибкам программного кода
 *
 * Представляет собой исключение кода приложения, а не пользовательского ввода
 */
final class SettingsLogicException extends LogicException
{
}
