<?php
namespace App\Repositories\TenantHasSetting;

use Illuminate\Support\ServiceProvider;

class TenantHasSettingRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\TenantHasSetting\TenantHasSettingInterface',
            'App\Repositories\TenantHasSetting\TenantHasSettingRepository'
        );
    }
}
