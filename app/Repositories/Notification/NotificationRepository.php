<?php
namespace App\Repositories\Notification;

use App\Repositories\Notification\NotificationInterface;
use App\Helpers\ResponseHelper;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\UserNotification;
use App\Repositories\User\UserRepository;
use App\Repositories\Mission\MissionRepository;
use Illuminate\Contracts\Mail\Mailer;

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
     * @var Illuminate\Contracts\Mail\Mailer
     */
    public $mailer;

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
     * @param  Illuminate\Contracts\Mail\Mailer $mailer
     * @param  App\Repositories\User\UserRepository $userRepository
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        Mailer $mailer,
        Notification $notification,
        NotificationType $notificationType,
        UserNotification $userNotification,
        UserRepository $userRepository,
        MissionRepository $missionRepository
    ) {
        $this->responseHelper = $responseHelper;
        $this->mailer = $mailer;
        $this->notification = $notification;
        $this->notificationType = $notificationType;
        $this->userNotification = $userNotification;
        $this->userRepository = $userRepository;
        $this->missionRepository = $missionRepository;
    }

    /*
     * Get notification type id
     *
     * @param string $type
     * @return int
     */
    public function getNotificationType(string $type): int
    {
        return $this->notificationType
        ->where(['notification_type' => $type])
        ->value('notification_type_id');
    }

    /*
     * Send notification
     *
     * @param array $notificationData
     * @return void
     */
    public function sendNotification(array $notificationData)
    {
        switch ($notificationData['notification_type_id']) {
            case 1:
                $inviteUser = $this->userRepository->find($notificationData['to_user_id']);
                $toEmail = $inviteUser->email;
                $fromUserName = $this->userRepository->getUserName($notificationData['user_id']);
                $missionName = $this->missionRepository->getMissionName(
                    $notificationData['mission_id'],
                    $inviteUser->language_id
                );
        
                $notify = $this->userNotification
                ->where(['user_id' => $notificationData['user_id'],
                'notification_type_id' => $notificationData['notification_type_id']])->first();
            
                if ($notify) {
                    $notificationData = array(
                        'notification_type_id' => $notificationData['notification_type_id'],
                        'user_id' => $notificationData['user_id'],
                        'to_user_id' => $notificationData['to_user_id'],
                        'mission_id' => $notificationData['mission_id'],
                    );
                    $notification = $this->notification->create($notificationData);
                }
                $data = array(
                        'missionName'=> $missionName,
                        'fromUserName'=> $fromUserName
                    );
                $this->mailer->send('invite', $data, function ($message) use ($toEmail) {
                    $message->to($toEmail)
                    ->subject(trans('messages.custom_text.MAIL_MISSION_RECOMMENDATION'));
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                });
                break;
        }
    }
}
