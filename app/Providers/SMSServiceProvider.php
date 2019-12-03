<?php

namespace App\Providers;

use App\Helpers\HuyiSMS;
use Illuminate\Support\ServiceProvider;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Helpers\Contracts\SMSContract', 'App\Helpers\HuyiSMS');
    }

    public function provides()
    {
        return ['App\Helpers\Contracts\SMSContract'];
    }
}
