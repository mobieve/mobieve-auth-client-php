<?php

namespace Mobieve\AuthClient\Providers;

use Illuminate\Support\ServiceProvider;
use Mobieve\AuthClient\Models\CustomClient;

class CustomClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('customclient', function ($app) {
            return new CustomClient();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return ['customclient'];
    }
}
