<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Lib\Singles\PatternLibrary;


final class Date extends Component
{

    public string $pattern = PatternLibrary::DATE;


    /**
     * Инициализирует сущность компонента
     *
     * @param string $title
     * @param string $name
     * @param string $required
     * @param string $interval доступный интервал для выбора дат:
     * -1 только прошедшие даты (включая текущую дату),
     *  0 любые даты,
     *  1 только будущие даты (включая текущую дату)
     */
    public function __construct(
        public string $title,
        public string $name,
        public string $required,
        public string $interval = '0'
    ) {}


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.forms.date');
    }
}
