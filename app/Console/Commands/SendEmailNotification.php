<?php

namespace App\Console\Commands;

use DB;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Helpers\Helpers;
use App\Models\Notification;
use App\Mail\NotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Repositories\News\NewsRepository;
use App\Repositories\Story\StoryRepository;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\Timesheet\TimesheetRepository;
use App\Repositories\StoryInvite\StoryInviteRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\MissionInvite\MissionInviteRepository;
use App\Repositories\MissionComment\MissionCommentRepository;
use App\Repositories\MissionApplication\MissionApplicationRepository;

class SendEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email-notification';

    protected $helpers;

    protected $tenant;

    protected $missionRepository;

    protected $notificationRepository;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This email notification to users";

    /**
     * Create a new command instance.
     * @codeCoverageIgnore
     *
     * @return void
     */
    public function __construct(
        Helpers $helpers,
        NotificationRepository $notificationRepository,
        MissionRepository $missionRepository,
        TenantOptionRepository $tenantOptionRepository,
        MessageRepository $messageRepository,
        MissionCommentRepository $missionCommentRepository,
        NewsRepository $newsRepository,
        MissionApplicationRepository $missionApplicationRepository,
        StoryRepository $storyRepository,
        TimesheetRepository $timesheetRepository,
        StoryInviteRepository $storyInviteRepository,
        MissionInviteRepository $missionInviteRepository
    ) {
        parent::__construct();
        $this->helpers = $helpers;
        $this->notificationRepository = $notificationRepository;
        $this->missionRepository = $missionRepository;
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->messageRepository = $messageRepository;
        $this->missionCommentRepository = $missionCommentRepository;
        $this->newsRepository = $newsRepository;
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->storyRepository = $storyRepository;
        $this->timesheetRepository = $timesheetRepository;
        $this->storyInviteRepository = $storyInviteRepository;
        $this->missionInviteRepository = $missionInviteRepository;
    }

    /**
     * Execute the console command.
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    public function handle()
    {
        $this->helpers->switchDatabaseConnection('mysql');
        $tenants = DB::select('select * from tenant 
        left join tenant_language on tenant.tenant_id = tenant_language.tenant_id 
        and tenant_language.default = 1        
        where tenant.tenant_id in (1000, 1797)');

        if (sizeof($tenants)) {
            $this->warn("\n\nTotal tenants : ". sizeof($tenants));
            foreach ($tenants as $tenant) {
                // Create connection of tenant one by one
                if ($this->createConnection($tenant->tenant_id) !== 0) {
                    try {
                        $this->sendEmail($tenant);
                    } catch (\Exception $e) {
                        $this->warn("\n \n Error while sending email notification :
                        $tenant->name (tenant id : $tenant->tenant_id)");
                        $this->error("\n\n".$e->getMessage());
                        continue;
                    }
                }
            }
            $this->info("\n \n All notifications sent!");
        } else {
            $this->warn("\n \n No tenant found");
        }
    }

    public function sendEmail($tenant)
    {
        $this->tenant = $tenant;
        // Get all email notification
        $notifications = $this->notificationRepository->getEmailNotifications();

        $this->info("\n\n => ".$tenant->name . ' have '. $notifications->count() . " notifications. \n");
        $bar = $this->output->createProgressBar($notifications->count());

        if ($notifications->count()) {
            $this->warn("\t Sending notification to users \n");
            $bar->start();
        }
        // Call function based on notification type
        foreach ($notifications as $notification) {
            $notificationType = $this->notificationRepository
            ->getNotificationType($notification->notification_type_id);
            
            $template = 'emails.notifications.'.$notificationType;
            
            $notificaionType = str_replace("_", " ", $notificationType);
            $notificationString = lcfirst(str_replace(" ", "", ucwords($notificaionType)));
            $data = $this->$notificationString($notification);
            $user = \App\User::whereUserId($notification->user_id)->first();

            try {
                if (sizeof($data)) {
                    Mail::to($user->email)->send(new NotificationMail($template, $data));
                    $notification->is_email_notification = 2;
                    $notification->save();
                }
            } catch (\Exception $e) {
                $notification->is_email_notification = -1;
                $notification->save();

                $this->error($e->getMessage());
                $this->error('Email notification not sent to user '. $user->email);
            }
            $bar->advance();
        }
        $bar->finish();
    }

    /**
     * Create connection with tenant's database
     * @codeCoverageIgnore
     *
     * @param int $tenantId
     * @return int
     */
    public function createConnection(int $tenantId): int
    {
        DB::purge('tenant');
        
        // Set configuration options for the newly create tenant
        Config::set(
            'database.connections.tenant',
            array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenantId,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            )
        );

        // Set default connection with newly created database
        DB::setDefaultConnection('tenant');

        try {
            DB::connection('tenant')->getPdo();
        } catch (\Exception $exception) {
            return 0;
        }

        return $tenantId;
    }

    /**
     * This function will return array with create mission data
     * @param Notification $notification
     * @return array
     */
    public function newMissions(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'New mission created';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Here need to call service function
        $tenantName = $this->tenant->name;
        $languageId = \App\User::whereUserId($notification->user_id)->first()->language_id;
        $tenantDefaultLangId = $this->tenant->language_id;

        // Get details
        $missionName = $this->missionRepository->getMissionTitle(
            $notification->entity_id,
            $languageId,
            $tenantDefaultLangId
        );
        
        // Create message
        $mailData['missionName'] = $missionName;
        
        return $mailData;
    }

    /**
     * Returns details for new message
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function newMessages(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'New message received';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $messageDetails = $this->messageRepository->getMessageDetail($notification->entity_id);
        
        // Create message
        $mailData['message_subject'] = trans('general.notification.NEW_MESSAGE')." - ".$messageDetails->subject;
        $mailData['message_body'] = $messageDetails->message;
        
        return $mailData;
    }

    /**
     * Returns details for my comments
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function myComments(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'Your comment on mission';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $commentDetails = $this->missionCommentRepository->getCommentDetail($notification->entity_id);
        
        $languageId = \App\User::whereUserId($notification->user_id)->first()->language_id;
        $tenantDefaultLangId = $this->tenant->language_id;

        // Get details
        $missionName = $this->missionRepository->getMissionTitle(
            $commentDetails->mission_id,
            $languageId,
            $tenantDefaultLangId
        );

        $date = Carbon::parse($commentDetails->created_at)
        ->setTimezone(config('constants.TIMEZONE'))->format(config('constants.FRONT_DATE_FORMAT'));
        $status = trans('general.notification_status.'.$notification->action);

        // Create message
        $icon = ($notification->action === config('constants.notification_status.PUBLISHED')) ?
        Config('constants.notification_icons.APPROVED') : Config('constants.notification_icons.DECLINED');
                
        $mailData['mission_name'] = 'Mission Name : ' . $missionName;
        $mailData['comment'] = $commentDetails->comment;
        $mailData['comment_details'] = trans('general.notification.COMMENT_OF')." "
        .$date." ".trans('general.notification.IS')." ".$status;
        return $mailData;
    }

    /**
     * Returns details for new news
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function newNews(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'New news published';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;


        $languageId = \App\User::whereUserId($notification->user_id)->first()->language_id;

        // Get details
        $newsTitle = $this->newsRepository->getNewsTitle(
            $notification->entity_id,
            $languageId
        );

        // Create message
        $mailData['news_title'] = trans('general.notification.NEW_NEWS')." - ".$newsTitle;

        return $mailData;
    }

    /**
     * Returns details for mission application
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function missionApplication(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'Your mission application update';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        $languageId = \App\User::whereUserId($notification->user_id)->first()->language_id;

        // Get details
        $missionId = $this->missionApplicationRepository->getMissionId($notification->entity_id);
        $tenantDefaultLangId = $this->tenant->language_id;

        $missionName = $this->missionRepository->getMissionTitle(
            $missionId,
            $languageId,
            $tenantDefaultLangId
        );
        $status = trans('general.notification_status.'.$notification->action);
        
        // Create message
        $mailData['application_status'] = trans('general.notification.VOLUNTEERING_REQUEST')." ".$status." ".
        trans('general.notification.FOR_THIS_MISSION')." ".$missionName;
        return $mailData;
    }

    /**
     * Returns details for my stories
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function myStories(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'Your story status';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $storyDetails = $this->storyRepository->getStoryDetail($notification->entity_id);
        $status = trans('general.notification_status.'.$notification->action);

        // Create message
        $mailData['story_details'] = trans('general.notification.STORY')." "
        .trans('general.notification.IS')." ".$status." - ".$storyDetails[0]['title'];
        return $mailData;
    }

    /**
     * Returns details for volunteering goals
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function volunteeringGoals(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'Your timesheet status';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $timesheetDetails = $this->timesheetRepository->getDetailOfTimesheetEntry($notification->entity_id);
        $formattedDate = Carbon::createFromFormat('m-d-Y', $timesheetDetails->date_volunteered);
        $date = Carbon::parse($formattedDate)->format('d/m/Y');
        $status = trans('general.notification_status.'.$notification->action);

        // Create message
        $mailData['volunteering_details'] = trans('general.notification.VOLUNTEERING_GOALS_SUBMITTED_THE')." "
        .$date." ".trans('general.notification.IS')." ".$status;
        return $mailData;
    }

    /**
     * Returns details for volunteering hours
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function volunteeringHours(Notification $notification): array
    {
        $mailData = [];
        $mailData['subject'] = 'Your timesheet status';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $timesheetDetails = $this->timesheetRepository->getDetailOfTimesheetEntry($notification->entity_id);
        $formattedDate = Carbon::createFromFormat('m-d-Y', $timesheetDetails->date_volunteered);
        $date = Carbon::parse($formattedDate)->format('d/m/Y');
        $status = trans('general.notification_status.'.$notification->action);

        // Create message
        $response['volunteering_details'] = trans('general.notification.VOLUNTEERING_HOURS_SUBMITTED_THE')." ".
        $date." ".trans('general.notification.IS')." ".$status;
        return $response;
    }

    /**
     * Returns details for recommonded story
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function recommendedStory(Notification $notification): array
    {
        $mailData = [];

        $emailNotificationInviteColleague = config('constants.tenant_settings.EMAIL_NOTIFICATION_INVITE_COLLEAGUE');
        $getActivatedTenantSettings = $this->getAllTenantSetting();

        if (!$getActivatedTenantSettings->contains('key', $emailNotificationInviteColleague)) {
            return $mailData;
        }
        
        $mailData['subject'] = 'Your story recommendation';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $inviteDetails = $this->storyInviteRepository->getDetails($notification->entity_id);
        $storyTitle = $inviteDetails->story->title;

        $colleagueLanguageId = \App\User::whereUserId($notification->user_id)->first()->language_id;

        // Create message
        $mailData['recommendation_details'] = $inviteDetails->fromUser->first_name.
        " ".$inviteDetails->fromUser->last_name." - "
        .trans('general.notification.RECOMMENDS_THIS_STORY')." - ".$storyTitle;
        $mailData['colleagueLanguage'] = $colleagueLanguageId;

        return $mailData;
    }

    /**
     * Returns details for recommonded mission
     *
     * @param App\Models\Notification $notification
     * @return array
     */
    public function recommendedMissions(Notification $notification): array
    {
        $mailData = [];

        $emailNotificationInviteColleague = config('constants.tenant_settings.EMAIL_NOTIFICATION_INVITE_COLLEAGUE');
        $getActivatedTenantSettings = $this->getAllTenantSetting();

        if (!$getActivatedTenantSettings->contains('key', $emailNotificationInviteColleague)) {
            return $mailData;
        }

        $mailData['subject'] = 'Your mission recommendation';
        $mailData['logo'] = $this->tenantOptionRepository
        ->getOptionWithCondition(['option_name' => 'custom_logo'])->option_value;

        // Get details
        $inviteDetails = $this->missionInviteRepository->getDetails($notification->entity_id);
        $languageId = \App\User::whereUserId($notification->user_id)->first()->language_id;
        $tenantDefaultLangId = $this->tenant->language_id;
        $colleagueLanguageId = \App\User::whereUserId($notification->user_id)->first()->language_id;

        $missionName = $this->missionRepository->getMissionTitle(
            $inviteDetails->mission->mission_id,
            $languageId,
            $tenantDefaultLangId
        );
        
        // Create message
        $mailData['recommendation_details'] = $inviteDetails->fromUser->first_name.
        " ".$inviteDetails->fromUser->last_name." - "
        .trans('general.notification.RECOMMENDS_THIS_MISSION')." - ".$missionName;
        $mailData['colleagueLanguage'] = $colleagueLanguageId;

        return $mailData;
    }

    /**
     * Get fetch all tenant settings detais
     *
     * @return mix
     */
    public function getAllTenantSetting()
    {
        $tenant = $this->tenant;
        // Connect master database to get tenant settings
        $this->helpers->switchDatabaseConnection('mysql');
        $db = app()->make('db');

        $tenantSetting = $db->table('tenant_has_setting')
        ->select(
            'tenant_has_setting.tenant_setting_id',
            'tenant_setting.key',
            'tenant_setting.tenant_setting_id',
            'tenant_setting.description',
            'tenant_setting.title'
        )
        ->leftJoin(
            'tenant_setting',
            'tenant_setting.tenant_setting_id',
            '=',
            'tenant_has_setting.tenant_setting_id'
        )
        ->whereNull('tenant_has_setting.deleted_at')
        ->whereNull('tenant_setting.deleted_at')
        ->where('tenant_id', $tenant->tenant_id)
        ->orderBy('tenant_has_setting.tenant_setting_id')
        ->get();

        // Connect tenant database
        $this->helpers->switchDatabaseConnection('tenant');
        
        return $tenantSetting;
    }
}
