<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;


final class Toggle extends Component
{


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $name
     * @param string $required
     * @param string $defaultValue значение по умолчанию:
     * -1 нет,
     * '' ничего не выбрано,
     *  1 да
     */
    public function __construct(
        public string $title,
        public string $name,
        public string $required,
        public string $defaultValue = ''
    ) {}


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.forms.toggle');
    }
}
