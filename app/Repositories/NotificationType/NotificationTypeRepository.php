<?php
namespace App\Repositories\NotificationType;

use App\Repositories\NotificationType\NotificationTypeInterface;
use App\Models\NotificationType;
use Illuminate\Database\Eloquent\Collection;

class NotificationTypeRepository implements NotificationTypeInterface
{
    /**
     * @var App\Models\NotificationType
     */
    private $notificationType;

    /**
     * Create a new notification type repository instance.
     *
     * @param  App\Models\NotificationType $notificationType
     * @return void
     */
    public function __construct(NotificationType $notificationType)
    {
        $this->notificationType = $notificationType;
    }

    /**
     * Get notification settings
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationSettings(int $userId): Collection
    {
        $notificationSettings = $this->notificationType
        ->selectRaw(
            "notification_type.notification_type,
            notification_type.notification_type_id,
            CASE WHEN user_notification.notification_type_id  IS NULL THEN '0' ELSE '1' END AS is_active"
        )
        ->leftJoin('user_notification', function ($join) use ($userId) {
            $join->on('notification_type.notification_type_id', '=', 'user_notification.notification_type_id')
            ->whereNull('user_notification.deleted_at')
            ->where('user_notification.user_id', $userId);
        })
        ->get();

        return $notificationSettings;
    }
}
