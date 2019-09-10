<?php

namespace App\Jobs;

use App\Models\Tenant;
use Queue;
use App\Traits\SendEmailTrait;
use App\Jobs\TenantDefaultLanguageJob;
use App\Jobs\TenantMigrationJob;
use Illuminate\Support\Facades\Log;

class TenantBackgroundJobsJob extends Job
{
    use SendEmailTrait;

    /**
     * @var App\Models\Tenant
     */
    protected $tenant;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Create a new job instance.
     * @param App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->tenant->update(
                [
                    'background_process_status' => config('constants.background_process_status.IN_PROGRESS')
                ]
            );

            // ONLY FOR DEVELOPMENT MODE. (PLEASE REMOVE THIS CODE IN PRODUCTION MODE)
            if (env('APP_ENV')=='local' || env('APP_ENV')=='testing') {
                dispatch(new TenantDefaultLanguageJob($this->tenant));
            }
        
            // Job dispatched to create new tenant's database and migrations
            dispatch(new TenantMigrationJob($this->tenant));
		
            // Copy local default_theme folder
			dispatch(new DownloadAssestFromS3ToLocalStorageJob($this->tenant->name));
            
            // Create assets folder for tenant on AWS s3 bucket
			dispatch(new CreateFolderInS3BucketJob($this->tenant));
			
            // Compile CSS file and upload on s3
            dispatch(new CompileScssFiles($this->tenant));

            $this->tenant->update(
                [
                    'background_process_status' => config('constants.background_process_status.COMPLETED')
                ]
            );
        } catch (\Exception $e) {
            Log::info('Exception in tenant background job execution '. $e);
            $this->tenant->update(
                [
                    'background_process_status' => config('constants.background_process_status.FAILED')
                ]
            );
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        $this->tenant->update(['background_process_status' => config('constants.background_process_status.FAILED')]);
        $this->sendEmailNotification(true);
    }

    /**
     * Send email notification to admin
     * @param bool $isFail
     * @return void
     */
    public function sendEmailNotification(bool $isFail = false)
    {
        $status = ($isFail===false) ? trans('messages.email_text.PASSED') : trans('messages.email_text.FAILED');
        $message = "<p> ".trans('messages.email_text.TENANT')." : " .$this->tenant->name. "<br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_STATUS')." : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_JOB_NOTIFICATION'); //path to the email template
        $params['subject'] = ($isFail)
        ?
        trans("messages.email_text.ERROR"). " : " .trans('messages.email_text.ON_BACKGROUND_JOBS'). " "
        . $this->tenant->name . " " .trans("messages.email_text.TENANT")
        :
        trans("messages.email_text.SUCCESS"). " : " .trans('messages.email_text.ON_BACKGROUND_JOBS'). " "
        . $this->tenant->name. " " .trans("messages.email_text.TENANT"); //optional

        $params['data'] = $data;

        $this->sendEmail($params);
    }
}
