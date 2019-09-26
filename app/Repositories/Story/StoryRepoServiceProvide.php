<?php

namespace App\Repositories\Story;

use Illuminate\Support\ServiceProvider;

class TimesheetRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\Story\StoryInterface',
            'App\Repositories\Story\StoryRepository'
        );
    }
}
