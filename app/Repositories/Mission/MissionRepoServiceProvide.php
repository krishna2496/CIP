<?php
namespace App\Repositories\Mission;

use Illuminate\Support\ServiceProvider;

class MissionRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\Mission\MissionInterface', 'App\Repositories\Mission\MissionRepository');
    }
}
