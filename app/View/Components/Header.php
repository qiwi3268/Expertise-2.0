<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\UsersData\User;


final class Header extends Component
{

    public string $fio;

    /**
     * Инициализирует сущность компонента
     *
     */
    public function __construct()
    {
        /** @var User $user*/
        $user = auth()->user();
        $this->fio = $user->fio->getLongFio();
    }


    /**
     * Возвращает view компонента
     *
     * @return View
     */
    public function render(): View
    {
        return view('x-components.header');
    }
}