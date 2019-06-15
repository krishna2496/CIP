<?php

namespace App\Repositories\Tenant;


use Illuminate\Support\ServiceProvider;


class TenantRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\Tenant\TenantInterface', 'App\Repositories\Tenant\TenantRepository');
    }
}