<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionInvite\MissionInviteRepository;
use App\Repositories\StoryInvite\StoryInviteRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Repositories\MissionComment\MissionCommentRepository;
use App\Models\Notification;
use App\Helpers\Helpers;
use Carbon\Carbon;

class NotificationService
{
    /**
     * @var App\Repositories\MissionInvite\MissionInviteRepository
     */
    public $missionInviteRepository;

    /**
     * @var App\Repositories\StoryInvite\StoryInviteRepository
     */
    public $storyInviteRepository;

    /**
     * @var App\Repositories\Timesheet\TimesheetRepository
     */
    public $timesheetRepository;

    /**
     * @var App\Repositories\MissionComment\MissionCommentRepository
     */
    public $missionCommentRepository;
    
    /**
     * @var App\Repositories\User\UserRepository
     */
    public $userRepository;

    /**
     * @var App\Repositories\Mission\MissionRepository
     */
    public $missionRepository;

    /**
     * @var App\Helpers\Helpers
     */
    public $helpers;

    /**
     * Create a new Notification repository instance.
     *
     * @param  App\Repositories\MissionInvite\MissionInviteRepository $missionInviteRepository
     * @param  App\Repositories\StoryInvite\StoryInviteRepository $storyInviteRepository
     * @param  App\Repositories\Timesheet\TimesheetRepository $timesheetRepository
     * @param  App\Repositories\MissionComment\MissionCommentRepository $missionCommentRepository
     * @param  App\Repositories\User\UserRepository $userRepository
     * @param  App\Repositories\Mission\MissionRepository $missionRepository
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        MissionInviteRepository $missionInviteRepository,
        StoryInviteRepository $storyInviteRepository,
        TimesheetRepository $timesheetRepository,
        MissionCommentRepository $missionCommentRepository,
        UserRepository $userRepository,
        MissionRepository $missionRepository,
        Helpers $helpers
    ) {
        $this->missionInviteRepository = $missionInviteRepository;
        $this->storyInviteRepository = $storyInviteRepository;
        $this->timesheetRepository = $timesheetRepository;
        $this->missionCommentRepository = $missionCommentRepository;
        $this->userRepository = $userRepository;
        $this->missionRepository = $missionRepository;
        $this->helpers = $helpers;
    }

    /**
     * Returns details for recommonded mission
     *
     * @param App\Models\Notification $notification
     * @param int $languageId
     * @param int $defaultTenantLanguageId
     * @param string $tenantName
     * @return array
     */
    public function recommendedMissions(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $notificationDetails = $this->missionInviteRepository->getDetails($notification->entity_id);

        $missionName = $this->missionRepository->getMissionTitle(
            $notificationDetails->mission->mission_id,
            $languageId,
            $defaultTenantLanguageId
        );
        
        // Create message
        $response['icon'] = empty($notificationDetails->fromUser->avatar)
        ? $this->helpers->getUserDefaultProfileImage($tenantName)
        : $notificationDetails->fromUser->avatar;
        $response['notification_string'] = $notificationDetails->fromUser->first_name.
        " ".$notificationDetails->fromUser->last_name." - "
        .trans('general.notification.RECOMMENDS_THIS_MISSION')." - ".$missionName;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/mission/'.$notificationDetails->mission->mission_id;
        return $response;
    }

    /**
     * Returns details for recommonded story
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function recommendedStory(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $notificationDetails = $this->storyInviteRepository->getDetails($notification->entity_id);

        $storyTitle = $notificationDetails->story->title;
       
        // Create message
        $response['icon'] = empty($notificationDetails->fromUser->avatar)
        ? $this->helpers->getUserDefaultProfileImage($tenantName)
        : $notificationDetails->fromUser->avatar;
        $response['notification_string'] = $notificationDetails->fromUser->first_name.
        " ".$notificationDetails->fromUser->last_name." - "
        .trans('general.notification.RECOMMENDS_THIS_STORY')." - ".$storyTitle;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/story/'.$notificationDetails->story->story_id;
        return $response;
    }
    
    /**
     * Returns details for volunteering hours
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function volunteeringHours(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $notificationDetails = $this->timesheetRepository->getDetailsOfTimesheetEntry($notification->entity_id);
        $formattedDate = Carbon::createFromFormat('m-d-Y', $notificationDetails->date_volunteered);
        $date = Carbon::parse($formattedDate)->format('d/m/Y');

        $status = strtolower($notificationDetails->timesheetStatus->status);
       
        // Create message
        $response['icon'] = 'plus_image';
        $response['notification_string'] = trans('general.notification.VOLUNTEERING_HOURS_SUBMITTED_THE')." ".
        $date." ".$status;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/timesheet';
        return $response;
    }

    /**
     * Returns details for volunteering goals
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function volunteeringGoals(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $notificationDetails = $this->timesheetRepository->getDetailsOfTimesheetEntry($notification->entity_id);
        $formattedDate = Carbon::createFromFormat('m-d-Y', $notificationDetails->date_volunteered);
        $date = Carbon::parse($formattedDate)->format('d/m/Y');
        $status = strtolower($notificationDetails->timesheetStatus->status);
       
        // Create message
        $response['icon'] = 'plus_image';
        $response['notification_string'] = trans('general.notification.VOLUNTEERING_HOURS_SUBMITTED_THE')." "
        .$date." ".$status;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/timesheet';
        return $response;
    }

    
    /**
     * Returns details for my comments
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function myComments(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $notificationDetails = $this->missionCommentRepository->getComment($notification->entity_id);
        $date = Carbon::parse($notificationDetails->created_at)
        ->setTimezone(config('constants.TIMEZONE'))->format(config('constants.FRONT_DATE_FORMAT'));
        $status = strtolower($notificationDetails->approval_status);
       
        // Create message
        $response['icon'] = 'plus_image';
        $response['notification_string'] = trans('general.notification.VOLUNTEERING_HOURS_SUBMITTED_THE')." "
        .$date." ".$status;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/comments/'.$notificationDetails->mission_id;
        return $response;
    }

    /**
     * Returns details for my stories
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function myStories(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $notificationDetails = $this->missionCommentRepository->getComment($notification->entity_id);
        $date = Carbon::parse($notificationDetails->created_at)
        ->setTimezone(config('constants.TIMEZONE'))->format(config('constants.FRONT_DATE_FORMAT'));
        $status = strtolower($notificationDetails->approval_status);
       
        // Create message
        $response['icon'] = 'plus_image';
        $response['notification_string'] = trans('general.notification.STORY')." ".$date." ".$status;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/story/list';
        return $response;
    }

    /**
     * Returns details for new message
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function newMessages(Notification $notification): array
    {
        // Get details
        
        // Create message
        $response['icon'] = 'plus_image';
        $response['notification_string'] = trans('general.notification.NEW_MESSAGE');
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/messages';
        return $response;
    }

    /**
     * Returns details for new mission
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function newMissions(
        Notification $notification,
        int $languageId,
        int $defaultTenantLanguageId,
        string $tenantName = null
    ): array {
        // Get details
        $missionName = $this->missionRepository->getMissionTitle(
            $notification->entity_id,
            $languageId,
            $defaultTenantLanguageId
        );

        // Create message
        $response['icon'] = 'plus_image';
        $response['notification_string'] = trans('general.notification.NEW_MISSION')." - ".$missionName;
        $response['is_read'] = $notification->is_read;
        $response['link'] = 'app/mission/'.$notification->entity_id;
        return $response;
    }
}
