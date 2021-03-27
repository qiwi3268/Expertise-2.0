<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

use App\Database\Query\Grammars\MySqlGrammar;
use Carbon\CarbonImmutable;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Использование в приложении по умолчанию CarbonImmutable
        Date::use(CarbonImmutable::class);
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::connection()->setQueryGrammar(new MySqlGrammar);
    }
}
