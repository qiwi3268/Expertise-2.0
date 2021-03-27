<?php

declare(strict_types=1);

namespace App\Providers\Ais;

use Illuminate\Support\ServiceProvider;


final class HelperServiceProvider extends ServiceProvider
{

    /**
     * Подключает все функции хэлперы
     *
     * @return void
     */
    public function register(): void
    {
        foreach (glob(app_path('helpers') . '/*.php') as $file) {
            require_once $file;
        }
    }


    /**
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
