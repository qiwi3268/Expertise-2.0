<?php

namespace App\Rules\Miscs;

use Illuminate\Contracts\Validation\Rule;
use App\Lib\Settings\Miscs\SingleMiscsManager;
use App\Repositories\Miscs\MiscRepository;


/**
 * Правила валидации одиночного справочника в контексте базы данных
 *
 */
final class SingleMiscDataBaseRule implements Rule
{

    /**
     * Конструктор класса
     *
     * @param string $selectedId
     */
    public function __construct(private string $selectedId)
    {}


    /**
     * Правила валидации
     *
     * @param string $attribute
     * @param string $miscAlias
     * @return bool
     */
    public function passes($attribute, $miscAlias): bool
    {
        $mgr = SingleMiscsManager::getInstance();

        $rep = new MiscRepository($mgr->getClassNameByAlias($miscAlias));

        return $rep->existsById($this->selectedId);
    }


    /**
     * Сообщение об ошибке
     *
     * @return string
     */
    public function message(): string
    {
        return "Одиночный справочник не существует в БД";
    }
}
