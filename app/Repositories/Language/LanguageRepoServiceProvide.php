<?php
namespace App\Repositories\Language;

use Illuminate\Support\ServiceProvider;

class LanguageRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\Language\LanguageInterface',
            'App\Repositories\Language\LanguageRepository'
        );
    }
}
