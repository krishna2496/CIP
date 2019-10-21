<?php
namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use App\Traits\SendEmailTrait;

class TenantDefaultLanguageJob extends Job
{
    use SendEmailTrait;
    /**
     * @var App\Models\Tenant
     */
    protected $tenant;

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
     * Create a new job instance
     *
     * @param App\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->emailMessage = trans("messages.email_text.JOB_PASSED_SUCCESSFULLY");
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Add default English and French language for tenant - Testing purpose
            $defaultData = array(
                ['language_id' => 1, 'default' => '1'],
                ['language_id' => 2, 'default' => '0']
            );
            foreach ($defaultData as $key => $data) {
                $this->tenant->tenantLanguages()->create($data);
            }
        } catch (\Exception $e) {
            $this->emailMessage = "Error while adding default language for tenant";
            $this->sendEmailNotification(true);
            $this->tenant->delete();
        }
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
        $message .= trans('messages.email_text.BACKGROUND_JOB_NAME')." :
        ".trans('messages.email_text.TENANT_DEFAULT_LANGUAGE')." <br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_STATUS')." : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_JOB_NOTIFICATION'); //path to the email template
        $params['subject'] = ($isFail) ? trans("messages.email_text.ERROR"). " : "
        .trans('messages.email_text.TENANT_DEFAULT_LANGUAGE')
        . " " . trans('messages.email_text.JOB_FOR'). " "  . $this->tenant->name  . " "
        .trans("messages.email_text.TENANT") :
        trans("messages.email_text.SUCCESS"). " : " .trans('messages.email_text.TENANT_DEFAULT_LANGUAGE'). " " . trans('messages.email_text.JOB_FOR') . $this->tenant->name. " " .trans("messages.email_text.TENANT"); //optional
        $params['data'] = $data;

        $this->sendEmail($params);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        $this->sendEmailNotification(true);
    }
}
