<?php

namespace App\Listeners\ActivityLog;

use App\Events\User\UserNotificationEvent;
use App\Notifiers\AppUserNotifiers\UserDatabaseNotifier;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
use App\User;
use App\Models\UserNotification;
use App\Repositories\Notification\NotificationRepository;

class UserActivityLogListner
{
    // use InteractsWithQueue;

    /**
     * @var App\Models\NotificationRepository
     */
    public $notificationRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UserNotificationEvent  $mission
     * @return void
     */
    public function handle(UserNotificationEvent $data)
    {
        // Checking user have activated notification setting or not
        if ($data->userId !== null) {
            $user = User::where('user_id', $data->userId)->first();
            $data->userId = $data->userId;
        } else {
            $users = User::all();
            foreach ($users as $userDetails) {
                $data->userId = $userDetails->user_id;
                $this->sendNotificationToUser($data);
            }
            return true;
        }
        $this->sendNotificationToUser($data);
        return true;
    }

    /**
     * Store notification data into database
     * @param UserNotificationEvent $data
     * @return void
     */
    public function sendNotificationToUser(UserNotificationEvent $data)
    {
        $isNotificationActive = $this->notificationRepository->userNotificationSetting(
            $data->userId,
            $data->notificationTypeId
        );
        if (!is_null($isNotificationActive)) {
            UserDatabaseNotifier::notify(
                $data->notificationTypeId,
                $data->entityId,
                $data->action,
                $data->userId
            );
        }
    }
}
