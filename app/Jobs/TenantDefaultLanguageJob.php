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
     * Create a new job instance
     *
     * @param App\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->emailMessage = 'Job passed successufully';
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
            $this->sendEmailNotification();
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
        $status = ($isFail) ? 'Failed' : 'Passed';

        $message = "<p> Tenant : " .$this->tenant->name. "<br>";
        $message .= "Background Job Name : Tenant Default Language <br>";
        $message .= "Background Job Status : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
        $params['subject'] = ($isFail) ? 'Error: Tenant Default Language Job For '.$this->tenant->name. ' Tenant' :
        'Success: Tenant Default Language Job For '.$this->tenant->name. ' Tenant'; //optional
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
