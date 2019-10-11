<?php
namespace App\Notifiers\AppUserNotifiers;

use App\User;

/**
* DatabaseNotifier
*/
class OneUserDatabaseNotifier
{
    public static function notify($notificationTypeId, $userId, $entityId, $action)
    {
        $user = User::where('user_id', $userId)->first();

        $data['notification_type_id'] = $notificationTypeId;
        $data['user_id'] = $userId;
        $data['entity_id'] = $entityId;
        $data['action'] = $action;
        
        $user->notification()->create($data);
    }
}
