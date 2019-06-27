<?php
namespace App\Repositories\UserCustomField;

use Illuminate\Support\ServiceProvider;

class UserCustomFieldRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\UserCustomField\UserCustomFieldInterface',
            'App\Repositories\UserCustomField\UserCustomFieldRepository'
        );
    }
}
