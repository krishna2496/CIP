<?php

namespace App\Jobs;

use Leafo\ScssPhp\Compiler;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;
use App\Helpers\EmailHelper;

class CompileScssFiles extends Job
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Models\Tenant
     */
    private $tenant;

    /**
     * @var string
     */
    private $emailMessage;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

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
        // Job will try to attempt only one time. If need to re-attempt then it will delete job from table
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path().'/app/'.$this->tenant->name.'/assets/scss'));

        $assetUrl = 'https://'.env("AWS_S3_BUCKET_NAME").'.s3.'
        .env("AWS_REGION", "eu-central-1").'.amazonaws.com/'.$this->tenant->name.'/assets/images';

        $importScss =
            '@import "_assets";
            $assetUrl: "'.$assetUrl.'";
            @import "_variables";
            @import "../../../../../node_modules/bootstrap/scss/bootstrap";
            @import "../../../../../node_modules/bootstrap-vue/src/index";
            @import "custom";';

        $css = $scss->compile($importScss);
        
        // Put compiled css file into local storage
        Storage::disk('local')->put($this->tenant->name.'\assets\css\style.css', $css);

        // Copy default theme folder to tenant folder on s3
        Storage::disk('s3')->put(
            $this->tenant->name.'/assets/css/style.css',
            Storage::disk('local')->get($this->tenant->name.'\assets\css\style.css')
        );
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
        $message .= trans('messages.email_text.BACKGROUND_JOB_NAME')." : "
        .trans('messages.email_text.COMPILE_SCSS_FILES') ." <br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_STATUS')." : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_JOB_NOTIFICATION'); //path to the email template
        $params['subject'] = ($isFail) ? trans("messages.email_text.ERROR")." : "
        .trans('messages.email_text.COMPILE_SCSS_FILES') . " " .trans('messages.email_text.JOB_FOR'). " "
        . $this->tenant->name . " ".trans("messages.email_text.TENANT") :
        trans("messages.email_text.SUCCESS").": " .trans('messages.email_text.COMPILE_SCSS_FILES'). " " .trans('messages.email_text.JOB_FOR'). $this->tenant->name. " " .trans("messages.email_text.TENANT"); //optional
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
