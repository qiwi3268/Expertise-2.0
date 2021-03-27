<?php

declare(strict_types=1);

namespace App\Lib\FormHandling\Units\Items\Miscs;

use App\Exceptions\Lib\FormHandling\FormLogicException;
use App\Exceptions\Lib\FormHandling\FormInvalidArgumentException;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Repositories\Miscs\MiscRepository;


/**
 * Справочник с возможностью множественного выбора
 *
 */
final class MultipleMisc extends MiscItem
{

    /**
     * @var int[]
     */
    private array $selectedIds;


    /**
     * Реализация абстрактного метода
     *
     * @param string $value
     * @return bool
     * @throws FormInvalidArgumentException
     */
    protected function validate(string $value): bool
    {
        if (!is_numeric_html_arr($value)) {
            return false;
        }

        $selectedIds = html_arr_decode($value);

        if (arr_has_duplicates($selectedIds)) {
            return false;
        }

        $rep = new MiscRepository($this->miscClassName);

        foreach ($selectedIds as $ind => $id) {

            $id = (int) $id;

            if (!$rep->existsById($id)) {
                return false;
            }
            $selectedIds[$ind] = $id;
        }
        $this->selectedIds = $selectedIds;
        return true;
    }


    /**
     * Возвращает массив выбранных id
     *
     * @return int[]
     * @throws FormLogicException
     */
    public function getSelectedIds(): array
    {
        return $this->selectedIds ??
            throw new FormLogicException('Массив выбранных id не инициализирован');
    }


    /**
     * Обрабатывает отношение N к N в контексте БД
     *
     * Если справочник заполнен, то синхронизирует данные с БД
     * Если справочник незаполнен, то удаляет отношения в БД
     *
     * @param BelongsToMany $relation
     * @return mixed результат вызова функции whenFilled
     */
    public function handleManyToManyRelation(BelongsToMany $relation): mixed
    {
        return $this->whenFilled(
            fn (self $misc) => $relation->sync($misc->getSelectedIds()),
            fn (self $misc) => $relation->detach()
        );
    }
}