<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;
use App\Helpers\DatabaseHelper;
use DB;
use App\Helpers\EmailHelper;

class CreateFolderInS3BucketJob extends Job
{
    /**
     * @var App\Models\Tenant
     */
    private $tenant;
    
    /**
     * @var App\Helpers\DatabaseHelper
     */
    protected $databaseHelper;

    /**
     * @var string
     */
    private $emailMessage;

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
     * @var App\Helpers\EmailHelper
     */
    private $emailHelper;

    /**
     * Create a new job instance.
     * @param App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->databaseHelper = new DatabaseHelper;
        $this->emailMessage = trans("messages.email_text.JOB_PASSED_SUCCESSFULLY");
        $this->emailHelper = new EmailHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        exec('aws s3 cp --recursive s3://'.config('constants.AWS_S3_BUCKET_NAME').
            '/'.config('constants.AWS_S3_DEFAULT_THEME_FOLDER_NAME').' s3://'
            .config('constants.AWS_S3_BUCKET_NAME').'/'
            .$this->tenant->name);
            
        // Insert default logo image in database
        if (Storage::disk('s3')->has($this->tenant->name.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').
        '/'.env('AWS_S3_IMAGES_FOLDER_NAME').
        '/'.config('constants.AWS_S3_LOGO_IMAGE_NAME'))) {
            $logoPathInS3 = 'https://s3.'.env('AWS_REGION').'.amazonaws.com/'.
                env('AWS_S3_BUCKET_NAME').'/'.$this->tenant->name.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').
                '/'.env('AWS_S3_IMAGES_FOLDER_NAME').'/'.config('constants.AWS_S3_LOGO_IMAGE_NAME');

            // Connect with tenant database
            $tenantOptionData['option_name'] = "custom_logo";
            $tenantOptionData['option_value'] = $logoPathInS3;

            // Create connection with tenant database
            $this->databaseHelper->connectWithTenantDatabase($this->tenant->tenant_id);
            DB::table('tenant_option')->insert($tenantOptionData);

            // Disconnect tenant database and reconnect with default database
            DB::disconnect('tenant');
            DB::reconnect('mysql');
            DB::setDefaultConnection('mysql');
        }
            
        if (Storage::disk('s3')->has($this->tenant->name.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').
        '/css/'.env('S3_CUSTOME_CSS_NAME'))) {
            $pathInS3 = 'https://s3.'.env('AWS_REGION').'.amazonaws.com/'.
                env('AWS_S3_BUCKET_NAME').'/'.$this->tenant->name.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').
                '/css/'.env('S3_CUSTOME_CSS_NAME');
                
            // Connect with tenant database
            $tenantOptionData['option_name'] = "custom_css";
            $tenantOptionData['option_value'] = $pathInS3;

            // Create connection with tenant database
            $this->databaseHelper->connectWithTenantDatabase($this->tenant->tenant_id);
            DB::table('tenant_option')->insert($tenantOptionData);

            // Disconnect tenant database and reconnect with default database
            DB::disconnect('tenant');
            DB::reconnect('mysql');
            DB::setDefaultConnection('mysql');
        }
    }

    /**
     * Send email notification to admin
     * @codeCoverageIgnore
     * @param bool $isFail
     * @return void
     */
    public function sendEmailNotification(bool $isFail = false)
    {
        $status = ($isFail===false) ? trans('messages.email_text.PASSED') : trans('messages.email_text.FAILED');
        $message = "<p> ".trans('messages.email_text.TENANT')." : " .$this->tenant->name. "<br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_NAME')." :
        ".trans('messages.email_text.CREATE_FOLDER_ON_S3_BUCKET')." <br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_STATUS')." : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_JOB_NOTIFICATION'); //path to the email template
        $params['subject'] = ($isFail) ? trans("messages.email_text.ERROR")." :
        ".trans('messages.email_text.CREATE_FOLDER_ON_S3_BUCKET'). " ". trans('messages.email_text.JOB_FOR').
        " "  . $this->tenant->name . " " .trans("messages.email_text.TENANT") :
        trans("messages.email_text.SUCCESS"). " : " .trans('messages.email_text.CREATE_FOLDER_ON_S3_BUCKET'). " " . trans('messages.email_text.JOB_FOR') . $this->tenant->name. " " .trans("messages.email_text.TENANT"); //optional
        $params['data'] = $data;

        $this->emailHelper->sendEmail($params);
    }

    /**
     * The job failed to process.
     * @codeCoverageIgnore
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        $this->sendEmailNotification(true);
    }
}
