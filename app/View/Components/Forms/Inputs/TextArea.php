<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use Illuminate\Contracts\View\View;


final class TextArea extends FormInput
{


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $name
     * @param string $required
     * @param string $maxLength
     */
    public function __construct(
        string $title,
        string $name,
        string $required,
        public string $maxLength
    ) {
        parent::__construct($title, $name, $required);
    }


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.forms.text-area');
    }
}
