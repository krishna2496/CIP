<?php
namespace App\Repositories\Notification;

use App\Repositories\Notification\NotificationInterface;
use App\Helpers\ResponseHelper;
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
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  App\Models\Notification $notification
     * @param  App\Models\NotificationType $notificationType
     * @param  App\Models\UserNotification $userNotification
     * @param  App\Repositories\MissionInvite\MissionInviteRepository $missionInviteRepository
     * @param  App\Repositories\User\UserRepository $userRepository
     * @param  App\Repositories\Mission\MissionRepository $missionRepository
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        Notification $notification,
        NotificationType $notificationType,
        UserNotification $userNotification,
        MissionInviteRepository $missionInviteRepository,
        UserRepository $userRepository,
        MissionRepository $missionRepository
    ) {
        $this->responseHelper = $responseHelper;
        $this->notification = $notification;
        $this->notificationType = $notificationType;
        $this->userNotification = $userNotification;
        $this->missionInviteRepository = $missionInviteRepository;
        $this->userRepository = $userRepository;
        $this->missionRepository = $missionRepository;
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
     * Get notifications count
     *
     * @param int $userId
     * @return int
     */
    public function getNotificationsCount(int $userId): int
    {
        return $this->notification->where(['user_id' => $userId, 'is_read' => '0'])->get()->count();
    }
}
