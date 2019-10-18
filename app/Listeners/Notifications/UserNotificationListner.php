<?php

namespace App\Listeners\Notifications;

use App\Events\User\UserNotificationEvent;
use App\Notifiers\AppUserNotifiers\UserDatabaseNotifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Models\UserNotification;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\Mission\MissionRepository;
use Illuminate\Support\Facades\Log;

class UserNotificationListner
{
    /**
     * @var App\Repositories\Notification\NotificationRepository
     */
    public $notificationRepository;

    /**
     * @var App\Repositories\Mission\MissionRepository
     */
    public $missionRepository;

    /**
     * Create the event listener.
     * @param NotificationRepository $notificationRepository
     * @return void
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        MissionRepository $missionRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->missionRepository = $missionRepository;
    }

    /**
     * Handle the event.
     *
     * @param  UserNotificationEvent  $mission
     * @return bool
     */
    public function handle(UserNotificationEvent $data): bool
    {
        if ($data->userId !== null) {
            $user = User::where('user_id', $data->userId)->first();
            $data->userId = $data->userId;
        } else {
            $users = User::all();
            foreach ($users as $userDetails) {
                $data->userId = $userDetails->user_id;
                $this->storeNotificationToDatabase($data);
            }
            return true;
        }
        $this->storeNotificationToDatabase($data);
        return true;
    }

    /**
     * Store notification data into database, if user have activated settings
     * @param UserNotificationEvent $data
     * @return void
     */
    public function storeNotificationToDatabase(UserNotificationEvent $data)
    {
        // Checking user have activated notification setting or not
        $isNotificationActive = $this->notificationRepository->userNotificationSetting(
            $data->userId,
            $data->notificationTypeId
        );
        if (config('constants.notification_type_keys.NEW_MISSIONS')
            === $this->notificationRepository->getNotificationType($data->notificationTypeId)
            && !is_null($isNotificationActive)
        ) {
            // This is new mission notification, need to check user's skill and availability match with mission or not.
            $isUserRelatedToMission = $this->missionRepository->checkIsMissionRelatedToUser(
                $data->entityId,
                $data->userId
            );
            if ($isUserRelatedToMission > 0) {
                $this->sendDatabaseNotification($data);
            }
        } else {
            if ($isNotificationActive) {
                $this->sendDatabaseNotification($data);
            }
        }
    }

    /**
     * Store notification data into database
     * @param UserNotificationEvent $data
     * @return void
     */
    public function sendDatabaseNotification(UserNotificationEvent $data)
    {
        UserDatabaseNotifier::notify(
            $data->notificationTypeId,
            $data->entityId,
            $data->action,
            $data->userId
        );
    }
}
