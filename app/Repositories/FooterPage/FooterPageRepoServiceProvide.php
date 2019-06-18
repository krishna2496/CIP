<?php

namespace App\Repositories\FooterPage;


use Illuminate\Support\ServiceProvider;


class FooterPageRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\FooterPage\FooterPageInterface', 'App\Repositories\FooterPage\FooterPageRepository');
    }
}