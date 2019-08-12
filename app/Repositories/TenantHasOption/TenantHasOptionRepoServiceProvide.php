<?php
namespace App\Repositories\TenantHasOption;

use Illuminate\Support\ServiceProvider;

class TenantHasOptionRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\TenantHasOption\TenantHasOptionInterface',
            'App\Repositories\TenantHasOption\TenantHasOptionRepository' 
        );
    }
}
