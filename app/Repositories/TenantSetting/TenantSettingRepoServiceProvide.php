<?php

namespace App\Repositories\TenantSetting;

use Illuminate\Support\ServiceProvider;

class TenantSettingRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\TenantSetting\TenantSettingInterface',
            'App\Repositories\TenantSetting\TenantSettingRepository'
        );
    }
}
