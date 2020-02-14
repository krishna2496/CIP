<?php

namespace App\Providers;

use App\Events\User\UserActivityLogEvent;
use App\Events\User\UserNotificationEvent;
use App\Listeners\ActivityLog\UserActivityLogListner;
use App\Listeners\Notifications\UserNotificationListner;
use App\Listeners\Notifications\UserEmailNotificationListner;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserNotificationEvent::class => [
            UserNotificationListner::class,
            UserEmailNotificationListner::class
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
