<?php
namespace App\Repositories\MissionInvite;

use Illuminate\Support\ServiceProvider;

class MissionInviteRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\MissionInvite\MissionInviteInterface',
            'App\Repositories\MissionInvite\MissionInviteRepository'
        );
    }
}
