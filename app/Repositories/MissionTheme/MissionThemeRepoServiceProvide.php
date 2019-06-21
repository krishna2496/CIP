<?php

namespace App\Repositories\MissionTheme;


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
        $this->app->bind('App\Repositories\MissionTheme\MissionThemeInterface', 'App\Repositories\MissionTheme\MissionThemeRepository');
    }
}