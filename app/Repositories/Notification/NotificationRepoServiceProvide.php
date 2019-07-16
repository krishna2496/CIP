<?php
namespace App\Repositories\Notification;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvide extends ServiceProvider
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
            'App\Repositories\Notification\NotificationInterface',
            'App\Repositories\Notification\NotificationRepository'
        );
    }
}
