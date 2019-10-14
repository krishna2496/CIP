<?php
namespace App\Notifiers\AppUserNotifiers;

use App\User;

/**
* DatabaseNotifier
*/
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
        $data['notification_type_id'] = $notificationTypeId;
        $data['entity_id'] = $entityId;
        $data['action'] = $action;

        if ($userId !== null) {
            $user = User::where('user_id', $userId)->first();
            $data['user_id'] = $userId;
        } else {
            $users = User::all();
            foreach ($users as $user) {
                $data['user_id'] = $user->user_id;
                self::sendNotificationToUser($data, $user);
            }
            return true;
        }
        self::sendNotificationToUser($data, $user);
        return true;
    }

    /**
     * Store notification data into database
     * @param array $data
     * @param App\User $user
     * @return void
     */
    public static function sendNotificationToUser(array $data, User $user)
    {
        // Here need to check user's notification settings
        $user->notification()->create($data);
    }
}
