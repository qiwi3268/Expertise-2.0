<?php

namespace App\Rules\Miscs;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Settings\Miscs\DependentMiscsManager;


/**
 * Правила валидации пары зависимых справочников в контексте файла настроек
 *
 */
final class DependentMiscsSettingsRule implements Rule
{

    /**
     * Конструктор класса
     *
     * @param string $subMiscAliases
     */
    public function __construct(private string $subMiscAliases)
    {
    }


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param string $mainMiscAlias
     * @return bool
     */
    public function passes($attribute, $mainMiscAlias): bool
    {
        $mgr = DependentMiscsManager::getInstance();

        foreach (html_arr_decode($this->subMiscAliases) as $subMiscAlias) {

            if (!$mgr->existsByAliases($mainMiscAlias, $subMiscAlias)) {

                return false;
            }
        }
        return true;
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return 'Зависимые справочники не определены в файле настроек';
    }
}
