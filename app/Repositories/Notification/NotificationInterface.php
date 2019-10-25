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

    /**
     * Read Unread notification by notification id
     *
     * @param int $notificationId
     * @param int $userId
     * @return int $updatedNotificationId
     */
    public function readUnreadNotificationById(int $notificationId, int $userId): int;

    /**
     * Delete user's all notifications
     * @param int $userId
     * @return bool
     */
    public function deleteAllNotifications($userId): bool;
}
