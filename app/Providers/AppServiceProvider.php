<?php

namespace App\Providers;

use LogicException;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('testing') && config('database.default') !== 'sqlite') {
            throw new LogicException(
                'The testing environment must use SQLite. Configure DB_CONNECTION=sqlite in .env.testing.'
            );
        }
    }
}
