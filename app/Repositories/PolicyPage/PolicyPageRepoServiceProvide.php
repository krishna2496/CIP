<?php
namespace App\Repositories\PolicyPage;

use Illuminate\Support\ServiceProvider;

class PolicyPageRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\PolicyPage\PolicyPageInterface',
            'App\Repositories\PolicyPage\PolicyPageRepository'
        );
    }
}
