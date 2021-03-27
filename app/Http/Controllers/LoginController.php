<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\UsersData\User;


class LoginController extends Controller
{


    /**
     * Страница формы ввода логина/пароля
     *
     * @return RedirectResponse|View
     */
    public function show(): RedirectResponse|View
    {
        if (Auth::check()) {

            return redirect()->to(route('navigation'));
        } else {

            /** @var View $view */
            $view = view('pages.login');

            return $view;
        }
    }


    /**
     * Вход в систему
     *
     * @param Request $req
     * @return RedirectResponse
     */
    public function login(Request $req): RedirectResponse
    {
        $email = $req->email;
        $password = $req->password;
        $remember = $req->boolean('remember', false);

        $user = User::where('email', $email)->first();

        if (
            isset($user)
            && Hash::check($password, $user->password)
        ) {

            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($password);
                $user->update();
            }

            // Создание сессии
            Auth::login($user, $remember);

            return redirect()->intended(route('navigation'));
        }

        return back()->withErrors([
            'authenticate' => 'Не подходит логин/пароль'
        ]);
    }
}
