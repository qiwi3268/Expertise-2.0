<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;


abstract class FormInput extends Component
{

    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $name
     * @param string $required
     */
    public function __construct(
        public string $title,
        public string $name,
        public string $required
    ) {}


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.forms.input');
    }
}