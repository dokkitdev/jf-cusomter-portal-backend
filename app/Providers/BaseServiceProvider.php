<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    /**
     * @codeCoverageIgnore
     */
    public function boot(): void
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function register()
    {
    }
}
