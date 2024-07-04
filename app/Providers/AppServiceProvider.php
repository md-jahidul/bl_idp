<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
        Passport::useClientModel('App\Models\Passport\Client');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Log::info($this->app->environment());
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
