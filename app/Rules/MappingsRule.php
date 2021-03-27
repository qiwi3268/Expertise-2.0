<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Settings\FileMappingsManager;


/**
 * Правила валидации файловых маппингов
 *
 */
final class MappingsRule implements Rule
{

    /**
     * Конструктор класса
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param string $mappings
     * @return bool
     */
    public function passes($attribute, $mappings): bool
    {
        return FileMappingsManager::validate($mappings);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return 'Полученные маппинги некорректны';
    }
}
