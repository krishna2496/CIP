<?php

namespace App\Repositories\Country;


use Illuminate\Support\ServiceProvider;


class CountryRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\Country\CountryInterface', 'App\Repositories\Country\CountryRepository');
    }
}