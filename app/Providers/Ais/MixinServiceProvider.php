<?php

declare(strict_types=1);

namespace App\Providers\Ais;

use ReflectionException;

use Illuminate\Support\ServiceProvider;
use Carbon\CarbonImmutable;
use App\Mixins\CarbonImmutableMixin;


final class MixinServiceProvider extends ServiceProvider
{

    /**
     *
     * @return void
     */
    public function register(): void
    {
    }


    /**
     * Предназначен для внедрения миксинов
     *
     * @return void
     * @throws ReflectionException
     */
    public function boot(): void
    {
        CarbonImmutable::mixin(new CarbonImmutableMixin);
    }
}
