<?php
namespace App\Repositories\UserFilter;

use Illuminate\Support\ServiceProvider;

class UserFilterRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\UserFilter\UserFilterInterface', 'App\Repositories\UserFilter\UserFilterRepository');
    }
}
