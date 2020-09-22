<?php
namespace App\Repositories\Notification;

use App\Repositories\Notification\NotificationInterface;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\MissionInvite\MissionInviteRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\Mission\MissionRepository;

class NotificationRepository implements NotificationInterface
{
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
     * @var App\Repositories\MissionInvite\MissionInviteRepository
     */
    public $missionInviteRepository;

    /**
     * @var App\Repositories\User\UserRepository
     */
    public $userRepository;

    /**
     * @var App\Repositories\Mission\MissionRepository
     */
    public $missionRepository;

    /**
     * Create a new Notification repository instance.
     *
     * @param  App\Models\Notification $notification
     * @param  App\Models\NotificationType $notificationType
     * @param  App\Models\UserNotification $userNotification
     * @param  App\Repositories\MissionInvite\MissionInviteRepository $missionInviteRepository
     * @param  App\Repositories\User\UserRepository $userRepository
     * @param  App\Repositories\Mission\MissionRepository $missionRepository
     * @return void
     */
    public function __construct(
        Notification $notification,
        NotificationType $notificationType,
        UserNotification $userNotification,
        MissionInviteRepository $missionInviteRepository,
        UserRepository $userRepository,
        MissionRepository $missionRepository
    ) {
        $this->notification = $notification;
        $this->notificationType = $notificationType;
        $this->userNotification = $userNotification;
        $this->missionInviteRepository = $missionInviteRepository;
        $this->userRepository = $userRepository;
        $this->missionRepository = $missionRepository;
    }

    /**
     * Get notification type id from type
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
     * Get notifications
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getNotifications(int $userId): Collection
    {
        return $this->notification->with('notificationType')->where(['user_id' => $userId])
        ->orderBy('notification.created_at', 'DESC')->get();
    }

    /**
     * Read Unread notification by notification id
     *
     * @param int $notificationId
     * @param int $userId
     * @return int $updatedNotificationId
     */
    public function readUnreadNotificationById(int $notificationId, int $userId): int
    {
        $notifications = $this->notification->where([
            'user_id' => $userId
        ])->findOrFail($notificationId);
        
        // found the notifications then update read/unread status
        if (!empty($notifications)) {
            $updateReadStatus = $notifications->is_read == config('constants.notification.read') ?
                config('constants.notification.unread') : config('constants.notification.read');

            $notifications->is_read = $updateReadStatus;
            $notifications->save();
        }
        return $notifications->notification_id;
    }

    /**
     * Delete user's all notifications
     *
     * @param int $userId
     * @return bool
     */
    public function deleteAllNotifications($userId): bool
    {
        return $this->notification->where([
            'user_id' => $userId
        ])->delete();
    }

    /**
     * Get notifications count
     *
     * @param int $userId
     * @return int
     */
    public function getNotificationsCount(int $userId): int
    {
        return $this->notification->where(['user_id' => $userId, 'is_read' => '0'])->get()->count();
    }

    /**
     * Get notification type from notification type id
     *
     * @param int $notificationTypeId
     * @return string
     */
    public function getNotificationType(int $notificationTypeId): string
    {
        return $this->notificationType
        ->where(['notification_type_id' => $notificationTypeId])
        ->value('notification_type');
    }

    /**
     * Get notification from notification type id
     *
     * @param int $notificationTypeId
     * @return App\Models\Notification
     */
    public function getNotificationByTypeId(int $notificationTypeId): Notification
    {
        return $this->notification->where(['notification_type_id' => $notificationTypeId])->first();
    }

    /**
     * Get all email notifications
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getEmailNotifications(): Collection
    {
        return $this->notification->where('is_email_notification', 1)->get();
    }

    /**
     * Delete mission related notifications
     *
     * @param int $missionId
     * @return bool
     */
    public function deleteMissionNotifications($missionId): bool
    {
        return $this->notification
        ->with(['notificationType' => function ($query) {
            $query->whereIn('notification_type', [
                config("constants.notification_type")["NEW_MISSIONS"],
                config("constants.notification_type")["MISSION_APPLICATION"],
                config("constants.notification_type")["RECOMMENDED_MISSIONS"],
                config("constants.notification_type")["VOLUNTEERING_HOURS"],
                config("constants.notification_type")["VOLUNTEERING_GOALS"],
                config("constants.notification_type")["MY_COMMENTS"],
                config("constants.notification_type")["MY_STORIES"],
                config("constants.notification_type")["MISSION_APPLICATION"]
            ]);
        }])
        ->where([
            'entity_id' => $missionId
        ])->delete();
    }

    /**
     * Delete news related notifications
     *
     * @param int $newsId
     * @return bool
     */
    public function deleteNewsNotifications($newsId): bool
    {
        return $this->notification
        ->with(['notificationType' => function ($query) {
            $query->whereIn('notification_type', [
                config("constants.notification_type")["NEW_NEWS"]
            ]);
        }])
        ->where([
            'entity_id' => $newsId
        ])->delete();
    }

    /**
     * Delete story related notifications
     *
     * @param int $storyId
     * @return bool
     */
    public function deleteStoryNotifications($storyId): bool
    {
        return $this->notification
        ->with(['notificationType' => function ($query) {
            $query->whereIn('notification_type', [
                config("constants.notification_type")["MY_STORIES"],
                config("constants.notification_type")["RECOMMENDED_STORY"]
            ]);
        }])
        ->where([
            'entity_id' => $storyId
        ])->delete();
    }

    /**
     * Delete comment related notifications
     *
     * @param int $commentId
     * @return bool
     */
    public function deleteCommentNotifications($commentId): bool
    {
        return $this->notification
        ->with(['notificationType' => function ($query) {
            $query->whereIn('notification_type', [
                config("constants.notification_type")["MY_COMMENTS"]
            ]);
        }])
        ->where([
            'entity_id' => $commentId
        ])->delete();
    }

    /**
     * Delete message related notifications
     *
     * @param int $messageId
     * @return bool
     */
    public function deleteMessageNotifications($messageId): bool
    {
        return $this->notification
        ->with(['notificationType' => function ($query) {
            $query->whereIn('notification_type', [
                config("constants.notification_type")["NEW_MESSAGES"]
            ]);
        }])
        ->where([
            'entity_id' => $messageId
        ])->delete();
    }
    
}
