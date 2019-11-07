<?php

namespace App\Repositories\NewsCategory;

use Illuminate\Support\ServiceProvider;

class NewsCategoryRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\NewsCategory\NewsCategoryInterface',
            'App\Repositories\NewsCategory\NewsCategoryRepository'
        );
    }
}
