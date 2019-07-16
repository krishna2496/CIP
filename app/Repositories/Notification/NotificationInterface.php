<?php
namespace App\Repositories\Notification;

use Illuminate\Http\Request;

interface NotificationInterface
{
    /**
     * Get notification type
     *
     * @param string $type
     * @return int
     */
    public function getNotificationType(string $type);

    /*
     * Send notification
     *
     * @param array $notificationData
     * @return void
     */
    public function sendNotification(array $notificationData);
}
