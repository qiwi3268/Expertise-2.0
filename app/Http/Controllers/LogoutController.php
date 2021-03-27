<?php


namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;


class LogoutController extends Controller
{

    /**
     * Выход из системы
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        auth()->logout();

        session()->invalidate();

        session()->regenerateToken();

        return redirect('/');
    }
}
