<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\View\Utils\JsTransfer as Transfer;


final class JsTransfer extends Component
{

    public string $jst;


    /**
     * Инициализирует сущность компонента
     *
     */
    public function __construct()
    {
        $this->jst = (string) Transfer::getInstance();
    }


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.js-transfer');
    }
}
