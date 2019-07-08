<?php
namespace App\Repositories\ApiUser;

use Illuminate\Support\ServiceProvider;

class ApiUserRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\ApiUser\ApiUserInterface', 'App\Repositories\ApiUser\ApiUserRepository');
    }
}
