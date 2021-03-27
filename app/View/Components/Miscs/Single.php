<?php

declare(strict_types=1);

namespace App\View\Components\Miscs;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\View\Utils\MiscsHelper;
use App\View\Utils\MiscItem;

final class Single extends Component
{

    /**
     * @var MiscItem[]
     */
    public array $items;


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $required
     * @param string $alias
     * @param string|null $subAliases алиасы зависимых справочников.
     * Если присутствуют, то справочник главным из пары зависимых справочников
     */
    public function __construct(
        public string $title,
        public string $required,
        public string $alias,
        public ?string $subAliases = null
    ) {
        $this->items = MiscsHelper::getSingleMiscItems($alias);
    }


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.miscs.single');
    }
}
