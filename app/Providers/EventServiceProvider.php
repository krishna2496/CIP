<?php

namespace App\Providers;

use App\Events\User\UserNotificationEvent;
use App\Listeners\Notifications\UserNotificationListner;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use App\Events\User\UserActivityLogEvent;
use App\Listeners\ActivityLog\UserActivityLogListner;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserNotificationEvent::class => [
            UserNotificationListner::class
        ],
        UserActivityLogEvent::class => [
            UserActivityLogListner::class
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
