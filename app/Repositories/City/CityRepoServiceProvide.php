<?php

namespace App\Repositories\City;

use Illuminate\Support\ServiceProvider;

class CityRepoServiceProvide extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\City\CityInterface', 'App\Repositories\City\CityRepository');
    }
}
