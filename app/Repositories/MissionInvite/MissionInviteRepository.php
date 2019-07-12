<?php
namespace App\Repositories\MissionInvite;

use App\Repositories\MissionInvite\MissionInviteInterface;
use App\Helpers\ResponseHelper;
use App\Models\MissionInvite;
use App\Models\MissionLanguage;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\UserNotification;
use App\User;
use Validator;
use PDOException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Mail\Mailer;

class MissionInviteRepository implements MissionInviteInterface
{
    /**
     * @var App\Models\Mission
     */
    public $mission;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Models\MissionInvite
     */
    public $missionInvite;

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
     * @var App\User
     */
    public $user;
    
    /**
     * Create a new MissionInvite repository instance.
     *
     * @param  App\Models\Mission $mission
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  App\Models\MissionInvite $missionInvite
     * @param  App\Models\Notification $notification
     * @param  App\Models\NotificationType $notificationType
     * @param  App\Models\UserNotification $userNotification
     * @param  Illuminate\Contracts\Mail\Mailer $mailer
     * @param  App\User $user
     * @return void
     */
    public function __construct(
        Mission $mission,
        ResponseHelper $responseHelper,
        MissionInvite $missionInvite,
        Notification $notification,
        NotificationType $notificationType,
        UserNotification $userNotification,
        MissionLanguage $missionLanguage,
        Mailer $mailer,
        User $user
    ) {
        $this->mission = $mission;
        $this->responseHelper = $responseHelper;
        $this->missionInvite = $missionInvite;
        $this->notification = $notification;
        $this->notificationType = $notificationType;
        $this->userNotification = $userNotification;
        $this->missionLanguage = $missionLanguage;
        $this->mailer = $mailer;
        $this->user = $user;
    }

    /*
     * Check mission is already added or not.
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return int
     */
    public function checkInviteMission(int $missionId, int $inviteUserId, int $fromUserId): int
    {
        $inviteCount = $this->missionInvite
        ->where(['mission_id' => $missionId, 'to_user_id' => $inviteUserId, 'from_user_id' => $fromUserId])
        ->count();
        return $inviteCount;
    }
    
    /*
     * Store a newly created resource into database
     *
     * @param int $missionId
     * @param int $inviteUserId
     * @param int $fromUserId
     * @return App\Models\MissionInvite
     */
    public function inviteMission(int $missionId, int $inviteUserId, int $fromUserId): MissionInvite
    {
        $mission = $this->mission->findOrFail($missionId);
        $inviteUser = $this->user->find($inviteUserId);
        $toEmail = $inviteUser->email;
        $fromUserName = $this->user->getUserName($fromUserId);
        
        $missionName = $this->missionLanguage->getMissionName($missionId, $inviteUser->language_id);
        $invite = $this->missionInvite
        ->create(['mission_id' => $missionId, 'to_user_id' => $inviteUserId, 'from_user_id' => $fromUserId]);
   
        $notify = $this->userNotification->where(['user_id' => $fromUserId, 'notification_type_id' => 1])->first();
        if ($notify) {
            $notificationData = array(
                'notification_type_id' => config('constants.notification_types.RECOMMENDED-MISSIONS'),
                'from_user_id' => $fromUserId,
                'user_id' => $inviteUserId,
                'mission_id' => $missionId,
            );
            $mission = $this->notification->create($notificationData);
        }
        $data = array(
                'missionName'=> $missionName,
                'fromUserName'=> $fromUserName
            );
        $this->mailer->send('invite', $data, function ($message) use ($toEmail) {
            $message->to($toEmail)
            ->subject('Mission Recommendation');
            $message->from('ciplatform@example.com', 'CI Platform');
        });
        return $invite;
    }
}
