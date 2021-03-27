<?php

declare(strict_types=1);

namespace App\View\Components\Miscs;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;


final class MultipleSub extends Component
{


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $required
     * @param string $alias
     * @param string $errorMessage
     */
    public function __construct(
        public string $title,
        public string $required,
        public string $alias,
        public string $errorMessage,
    ) {}


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.miscs.multiple-sub');
    }
}
