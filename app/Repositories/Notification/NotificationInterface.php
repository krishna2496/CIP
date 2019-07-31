<?php
namespace App\Repositories\Notification;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;

interface NotificationInterface
{
    /**
     * Get notification type
     *
     * @param string $type
     * @return int
     */
    public function getNotificationTypeID(string $type): int;

    /**
     * Send notification
     *
     * @param array $notificationData
     * @return App\Models\Notification
     */
    public function createNotification(array $notificationData): Notification;
    
    /**
     * Check if user notification is enabled or not
     *
     * @param int $userId
     * @param int $notificationTypeId
     * @return null|App\Models\UserNotification
     */
    public function userNotificationSetting(int $userId, int $notificationTypeId): ?UserNotification;
}
