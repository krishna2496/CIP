<?php
namespace App\Repositories\Notification;

use App\Repositories\Notification\NotificationInterface;
use App\Helpers\ResponseHelper;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\UserNotification;

class NotificationRepository implements NotificationInterface
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Models\Notification
     */
    public $notification;

    /**
     * @var App\Models\NotificationType
     */
    public $notificationType;

    /**
     * @var App\Models\UserNotification
     */
    public $userNotification;

    /**
     * Create a new Notification repository instance.
     *
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  App\Models\Notification $notification
     * @param  App\Models\NotificationType $notificationType
     * @param  App\Models\UserNotification $userNotification
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        Notification $notification,
        NotificationType $notificationType,
        UserNotification $userNotification
    ) {
        $this->responseHelper = $responseHelper;
        $this->notification = $notification;
        $this->notificationType = $notificationType;
        $this->userNotification = $userNotification;
    }

    /**
     * Get notification type id
     *
     * @param string $type
     * @return int
     */
    public function getNotificationTypeID(string $type): int
    {
        return $this->notificationType
        ->where(['notification_type' => $type])
        ->value('notification_type_id');
    }

    /**
     * Send notification
     *
     * @param array $notificationData
     * @return App\Models\Notification
     */
    public function createNotification(array $notificationData): Notification
    {
        return $this->notification->create($notificationData);
    }
    
    /**
     * Check if user notification is enabled or not
     *
     * @param int $userId
     * @param int $notificationTypeId
     * @return null|App\Models\UserNotification
     */
    public function userNotificationSetting(int $userId, int $notificationTypeId): ?UserNotification
    {
        return $this->userNotification->where(['user_id' => $userId,
                'notification_type_id' => $notificationTypeId])->first();
    }

    /**
     * Get notification type id
     *
     * @param string $type
     * @return string
     */
    public function getNotificationType(int $notificationTypeId): string
    {
        return $this->notificationType
        ->where(['notification_type_id' => $notificationTypeId])
        ->value('notification_type');
    }
}
