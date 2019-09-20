<?php

namespace App\Repositories\News;

use Illuminate\Support\ServiceProvider;

class NewsRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\News\NewsInterface',
            'App\Repositories\News\NewsRepository'
        );
    }
}
