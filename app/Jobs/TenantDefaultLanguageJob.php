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
     * Create a new job instance
     *
     * @param App\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {
        // Job will try to attempt only one time. If need to re-attempt then it will delete job from table
        if ($this->attempts() < 2) {
            // do job things
            try {
                // Add default English and French language for tenant - Testing purpose
                $defaultData = array(
                    ['language_id' => 1, 'default' => '1'],
                    ['language_id' => 2, 'default' => '0']
                );
                foreach ($defaultData as $key => $data) {
                    $this->tenant->tenantLanguages()->create($data);
                }
                // Send success mail to super admin
                $message = "<p> Tenant : " .$this->tenant->name. "<br>";
                $message .= "Background Job Name : Tenant Default Language Job <br>";
                $message .= "Background Job Status : Success <br>";

                $this->sendEmailNotification($message);

                Log::info($message);
            } catch (\Exception $e) {
                $message = "<p> Tenant : " .$this->tenant->name. '<br>';
                $message .= "Background Job Name : Tenant Default Language Job <br>";
                $message .= "Background Job Status : Failed <br>";
                $message .= "Message : Tenant has been delete.</p>";

                $this->sendEmailNotification($message, true);

                Log::info($message);

                // Delete tenant from database
                $this->tenant->delete();
            }
        } else {
            $message = "<p> Tenant : " .$this->tenant->name. "<br>";
            $message .= "Background Job Name : Tenant Default Language <br>";
            $message .= "Background Job Status : Failed <br>";
            $message .= "Message : Background job has been deleted from database.</p>";

            $this->sendEmailNotification($message, true);

            Log::info($message);

            // Delete job from database
            $this->delete();
        }
    }

    /**
     * Send email notification to admin
     * @param string $message
     * @param bool $isFail
     * @return void
     */
    public function sendEmailNotification(string $message, bool $isFail = false)
    {
        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );
        
        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
        $params['subject'] = ($isFail) ? 'Error in tenant creation : '. $this->tenant->name :
        'Success tenant default language job : '.$this->tenant->name; //optional
        $params['data'] = $data;

        $this->sendEmail($params);
    }
}
