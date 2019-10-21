<?php

namespace App\Repositories\TenantActivatedSetting;

use Illuminate\Support\ServiceProvider;

class TenantActivatedSettingRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\TenantActivatedSetting\TenantActivatedSettingInterface',
            'App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository'
        );
    }
}
