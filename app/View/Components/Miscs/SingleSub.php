<?php

declare(strict_types=1);

namespace App\View\Components\Miscs;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;


final class SingleSub extends Component
{


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $required
     * @param string $alias
     * @param string $errorMessage
     * @param string|null $subAliases алиасы зависимых справочников.
     * Если присутствуют, то справочник является одновременно и зависимым и главным
     */
    public function __construct(
        public string $title,
        public string $required,
        public string $alias,
        public string $errorMessage,
        public ?string $subAliases = null
    ) {}


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.miscs.single-sub');
    }
}
