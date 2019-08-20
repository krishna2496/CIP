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
     * Create a new job instance.
     *
     * @param  App\Tenant $tenant Tenant model object
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->databaseHelper = new DatabaseHelper;
        $this->emailMessage = 'Job passed successufully';
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

            // Send success mail to super admin
            $this->sendEmailNotification();
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
     * @return void
     */
    public function sendEmailNotification(bool $isFail = false)
    {
        $status = ($isFail) ? 'Failed' : 'Passed';

        $message = "<p> Tenant : " .$this->tenant->name. "<br>";
        $message .= "Background Job Name : Tenant Migration <br>";
        $message .= "Background Job Status : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
        $params['subject'] = ($isFail) ? 'Error: Tenant Migration Job For '.$this->tenant->name. ' Tenant' :
        'Success: Tenant Migration Job For '.$this->tenant->name. ' Tenant'; //optional
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
