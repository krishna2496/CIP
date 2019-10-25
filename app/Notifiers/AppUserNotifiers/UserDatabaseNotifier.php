<?php
namespace App\Notifiers\AppUserNotifiers;

use App\User;
use App\Repositories\Notification\NotificationRepository;

class UserDatabaseNotifier
{

    /**
     * Store notification to database
     *
     * @param int $notificationTypeId
     * @param int $entityId
     * @param string $action
     * @param int|null $userId
     *
     * @return bool
     */
    public static function notify(
        int $notificationTypeId,
        int $entityId,
        string $action,
        int $userId = null
    ): bool {
        $user = User::where('user_id', $userId)->first();

        $data['notification_type_id'] = $notificationTypeId;
        $data['entity_id'] = $entityId;
        $data['action'] = $action;
        $data['user_id'] = $userId;
        if ($user->notification()->create($data)) {
            return true;
        }
        return false;
    }
}
