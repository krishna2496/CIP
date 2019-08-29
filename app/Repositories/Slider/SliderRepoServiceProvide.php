<?php

namespace App\Repositories\Slider;

use Illuminate\Support\ServiceProvider;

class SliderRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\Slider\SliderInterface', 'App\Repositories\Slider\SliderRepository');
    }
}
