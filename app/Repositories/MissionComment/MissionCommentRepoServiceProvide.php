<?php
namespace App\Repositories\MissionComment;

use Illuminate\Support\ServiceProvider;

class MissionThemeRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\MissionComment\MissionCommentInterface',
            'App\Repositories\MissionComment\MissionCommentRepository'
        );
    }
}
