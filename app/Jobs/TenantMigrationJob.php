<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\DatabaseHelper;
use App\Models\Tenant;
use DB;
use App\Traits\SendEmailTrait;

class TenantMigrationJob extends Job
{
    use SendEmailTrait;

    /**
     * @var App\Models\Tenant
     */
    protected $tenant;

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
     * Create a new job instance.
     *
     * @param  App\Tenant $tenant Tenant model object
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->databaseHelper = new DatabaseHelper;
        $this->emailMessage = trans("messages.email_text.JOB_PASSED_SUCCESSFULLY");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Create database
            DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$this->tenant->tenant_id}`");

            // Connect with newly created database
            $this->databaseHelper->connectWithTenantDatabase($this->tenant->tenant_id);
            
            // Call artisan command to create table for newly created tenant's database
            Artisan::call('migrate --path=database/migrations/tenant');

            // Call artisan command to run database seeder for default values
            Artisan::call('db:seed');
            
            // Disconnect and reconnect with default database
            DB::disconnect('tenant');
            DB::reconnect('mysql');
            DB::setDefaultConnection('mysql');
        } catch (\Exception $e) {
            $this->emailMessage = 'Error while creating migration for tenant';
            $this->sendEmailNotification(true);
            $this->tenant->delete();
            DB::statement("DROP DATABASE IF EXISTS `ci_tenant_{$this->tenant->tenant_id}`");
        }
    }

    /**
     * Send email notification to admin
     * @param bool $isFail
     * @param bool $errorMessage
     * @return void
     */
    public function sendEmailNotification(bool $isFail = false, string $errorMessage = '')
    {
        $status = ($isFail===false) ? trans('messages.email_text.PASSED') : trans('messages.email_text.FAILED');
        $message = "<p> ".trans('messages.email_text.TENANT')." : " .$this->tenant->name. "<br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_NAME')." : ".trans('messages.email_text.TENANT_MIGRATION')
        ." <br>";
        $message .= trans('messages.email_text.BACKGROUND_JOB_STATUS')." : ".$status." <br>";
        if (!empty($errorMessage)) {
            $message .= trans('messages.email_text.BACKGROUND_JOB_EXCEPTION_MESSAGE')." : ".$errorMessage." <br>";
        }
        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_JOB_NOTIFICATION'); //path to the email template
        $params['subject'] = ($isFail) ? trans("messages.email_text.ERROR"). " : "
        .trans('messages.email_text.TENANT_MIGRATION'). " "
        . trans('messages.email_text.JOB_FOR') . " "  . $this->tenant->name . " " .trans("messages.email_text.TENANT") :
        trans("messages.email_text.SUCCESS"). " : " .trans('messages.email_text.TENANT_MIGRATION'). " " . trans('messages.email_text.JOB_FOR') . $this->tenant->name. " " .trans("messages.email_text.TENANT"); //optional
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
        $this->sendEmailNotification(true, $exception->getMessage());
    }
}
