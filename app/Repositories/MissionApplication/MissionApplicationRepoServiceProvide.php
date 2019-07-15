<?php
namespace App\Repositories\MissionApplication;

use Illuminate\Support\ServiceProvider;

class MissionApplicationRepoServiceProvide extends ServiceProvider
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
        $this->app->bind(
            'App\Repositories\MissionApplication\MissionApplicationInterface',
            'App\Repositories\MissionApplication\MissionApplicationRepository'
        );
    }
}
