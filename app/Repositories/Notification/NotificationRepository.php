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
     * Get notification type id
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
     * Get notification details
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationDetails(
        string $notificationType,
        int $entityId,
        int  $languageId,
        int $defaultTenantLanguage
    ) {
        // Check notification type
        switch ($notificationType) {
            case config('constants.notification_type_keys.RECOMMENDED_MISSIONS'):
                //Recommended mission: Photo - name surname - *Recommends this mission* - mission name
                $fromUserId = $this->missionInviteRepository->getUserId($entityId);
                $missionId = $this->missionInviteRepository->getMissionId($entityId);
                $userDetails = $this->userRepository->find($fromUserId);
                $missionName = $this->missionRepository->getMissionTitle(
                    $missionId,
                    $languageId,
                    $defaultTenantLanguage
                );

                $response['icon'] = $userDetails['avatar'];
                $response['notification_string'] = $userDetails['first_name']." ".$userDetails['last_name']." - "
                .trans('general.notification.RECOMMENDS_THIS_MISSION')." - ".$missionName;
               
                return $response;
                break;
            
            case config('constants.notification_type_keys.VOLUNTEERING_HOURS'):
                // Volunteering hours approved (or declined):
                // Icon - *Volunteering hours submitted the* dd/mm/yyyy approved
                return $notificationType;
                break;

            case config('constants.notification_type_keys.VOLUNTEERING_GOALS'):
                // Volunteering goals approved (or declined):
                // Icon - *Volunteering goals submitted the* dd/mm/yyyy approved
                return $notificationType;
                break;

            case config('constants.notification_type_keys.MY_COMMENTS'):
                // My comment is approved (or declined): Icon - *Comment of* dd/mm/yyyy approved
                return $notificationType;
                break;

            case config('constants.notification_type_keys.MY_STORIES'):
                //My Story is approved (or declined): Icon - *Story* approved - title
                return $notificationType;
                break;
            
            case config('constants.notification_type_keys.NEW_MISSIONS'):
                // New missions: *New mission* - mission title
                return $notificationType;
                break;

            case config('constants.notification_type_keys.NEW_MESSAGES'):
                // New messages: *New Message* - message title
                return $notificationType;
                break;

            case config('constants.notification_type_keys.RECOMMENDED_STORY'):
                // Recommended story: Photo - name surname - *Recommends this story* - mission name
                return $notificationType;
                break;
        }
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
        
        // found the notifications then update read/ unread status
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
     * @param int $userId
     * @return bool
     */
    public function deleteAllNotifications($userId): bool
    {
        return $this->notification->where([
            'user_id' => $userId
        ])->delete();
    }
}
