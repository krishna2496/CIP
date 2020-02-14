<?php

namespace App\Listeners\Notifications;

use App\User;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Mail\NotificationMail;
use App\Helpers\LanguageHelper;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Events\User\UserNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Mission\MissionRepository;
use App\Notifiers\AppUserNotifiers\UserDatabaseNotifier;
use App\Repositories\Notification\NotificationRepository;

class UserEmailNotificationListner implements ShouldQueue
{
    /**
     * @var App\Repositories\Notification\NotificationRepository
     */
    public $notificationRepository;

    /**
     * @var App\Repositories\Mission\MissionRepository
     */
    public $missionRepository;

    /**
     * @var string
     */
    public $notificationType;

    /**
     * @var UserNotificationEvent
     */
    public $userNotificationEventData;

    public $helpers;
    public $languageHelper;
    public $request;

    /**
     * Create the event listener.
     * @param NotificationRepository $notificationRepository
     * @return void
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        MissionRepository $missionRepository,
        Helpers $helpers,
        LanguageHelper $languageHelper,
        Request $request
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->missionRepository = $missionRepository;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
        
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  UserNotificationEvent  $mission
     * @return bool
     */
    public function handle(UserNotificationEvent $data): bool
    {
        $this->notificationType = $this->notificationRepository->getNotificationType($data->notificationTypeId);
        $this->userNotificationEventData = $data;

        if ($data->userId !== null) {
            $user = User::where('user_id', $data->userId)->first();
            $data->userId = $data->userId;
        } else {
            $users = User::all();
            foreach ($users as $userDetails) {
                $data->userId = $userDetails->user_id;
                $this->storeNotificationToDatabase($data);
            }
            return true;
        }
        $this->storeNotificationToDatabase($data);
        return true;
    }

    /**
     * Store notification data into database, if user have activated settings
     * @param UserNotificationEvent $data
     * @return void
     */
    public function storeNotificationToDatabase(UserNotificationEvent $data)
    {
        // Checking user have activated email notification setting or not
        $isEmailNotificationActive = $this->notificationRepository->userNotificationSetting(
            $data->userId,
            $data->notificationTypeId
        );
        if (config('constants.notification_type_keys.NEW_MISSIONS')
            === $this->notificationType
            && !is_null($isEmailNotificationActive)
        ) {
            // This is mission create notification,
            // here need to check user's skill and availability match with mission or not.
            $isUserRelatedToMission = $this->missionRepository->checkIsMissionRelatedToUser(
                $data->entityId,
                $data->userId
            );
            if ($isUserRelatedToMission > 0) {
                $this->sendEmailNotification($data);
            }
        } else {
            if ($isEmailNotificationActive) {
                $this->sendEmailNotification($data);
            }
        }
        $this->sendEmailNotification($data);
    }

    /**
     * Send email notification to user
     * @param UserNotificationEvent $data
     * @return void
     */
    public function sendEmailNotification(UserNotificationEvent $data)
    {
        $mailData = [];
        // $template = 'emails.notifications.'.$this->notificationType;
        $template = 'emails.notifications.all-in-one';
        $mailTemplateFromDb = '<div style="width:200px;background:#CCC"><h1>[[MISSION_NAME]]</h1></div>';

        $notificaionType = str_replace("_", " ", $this->notificationType);
        $notificationString = str_replace(" ", "", ucwords($notificaionType));
        
        /* Here we call dynamic function based on notification type name */
        $mailData = $this->$notificationString($mailTemplateFromDb);
        Mail::to('siddharajsinh.zala@tatvasoft.com')->send(new NotificationMail($template, $mailData));
    }

    /**
     * This function will return array with create mission data
     * @return array
     */
    public function newMissions($mailTemplateFromDb): array
    {
        // Here need to call service function 
        $tenantName = $this->helpers->getSubDomainFromRequest($this->request);
        $languageId = $this->languageHelper->getLanguageId($this->request);
        $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($this->request);
        
        $mailData = [];
        $mailData['subject'] = 'New mission created';
        
        $notification = $this->notificationRepository
        ->getNotificationByTypeId($this->userNotificationEventData->notificationTypeId);
        // Get details
        $missionName = $this->missionRepository->getMissionTitle(
            $this->userNotificationEventData->entityId,
            $languageId,
            $defaultTenantLanguage->language_id
        );
        
        // Create message
        $mailData['body'] =
            str_replace(
                '[[MISSION_NAME]]',
                $missionName,
                $mailTemplateFromDb
            );
        
        return $mailData;
    }
    
    /**
     * This function will return array with  recommendedMissions data
     * @return array
     */
    public function recommendedMissions(): array
    {
        $mailData = [];
        $mailData['subject'] = 'New recommended mission here';
        return $mailData;
    }

    /**
     * This function will return array with  recommendedStory data
     * @return array
     */
    public function recommendedStory(): array
    {
        $mailData = [];
        $mailData['subject'] = 'new recommended story here';
        return $mailData;
    }

    /**
     * This function will return array with  volunteeringHours data
     * @return array
     */
    public function volunteeringHours(): array
    {
        $mailData = [];
        $mailData['subject'] = 'My volunteering hours done';
        return $mailData;
    }

    /**
     * This function will return array with  volunteeringGoals data
     * @return array
     */
    public function volunteeringGoals(): array
    {
        $mailData = [];
        $mailData['subject'] = 'My volunteering goal done';
        return $mailData;
    }

    /**
     * This function will return array with  myComments data
     * @return array
     */
    public function myComments(): array
    {
        $mailData = [];
        $mailData['subject'] = 'My comment approved';
        return $mailData;
    }

    /**
     * This function will return array with  myStories data
     * @return array
     */
    public function myStories(): array
    {
        $mailData = [];
        $mailData['subject'] = 'My story published';
        return $mailData;
    }

    /**
     * This function will return array with  newMessages data
     * @return array
     */
    public function newMessages(): array
    {
        $mailData = [];
        $mailData['subject'] = 'New message received';
        return $mailData;
    }
    
    /**
     * This function will return array with  newNews data
     * @return array
     */
    public function newNews(): array
    {
        $mailData = [];
        $mailData['subject'] = 'New news published';
        return $mailData;
    }

    /**
     * This function will return array with  missionApplication data
     * @return array
     */
    public function missionApplication(): array
    {
        $mailData = [];
        $mailData['subject'] = 'Mission application approved';
        return $mailData;
    }
}
