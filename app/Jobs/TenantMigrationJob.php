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
     * Create a new job instance.
     *
     * @param  App\Tenant $tenant Tenant model object
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->databaseHelper = new DatabaseHelper;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Job will try to attempt only one time. If need to re-attempt then it will delete job from table
        if ($this->attempts() < 2) {
            // do job things
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
                $message = "<p> Tenant : " .$this->tenant->name. "<br>";
                $message .= "Background Job Name : Tenant Migration Job <br>";
                $message .= "Background Job Status : Success <br>";

                $this->sendEmailNotification($message);

                Log::info($message);
            } catch (\Exception $e) {
                $message = "<p> Tenant : " .$this->tenant->name. "<br>";
                $message .= "Background Job Name : Tenant Migration Job <br>";
                $message .= "Background Job Status : Failed <br>";
                $message .= "Message : Tenant and database have been deleted. </p>";
    
                $this->sendEmailNotification($message, true);
    
                Log::info($message);
                                
                // Delete created tenant
                $this->tenant->delete();
                // Drop tanant database
                DB::statement("DROP DATABASE IF EXISTS `ci_tenant_{$this->tenant->tenant_id}`");
            }
        } else {
            $message = "<p> Tenant : " .$this->tenant->name. "<br>";
            $message .= "Background Job Name : Tenant Migration Job <br>";
            $message .= "Background Job Status : Failed <br>";
            $message .= "Message : Job deleted from database. </p>";

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
        'Success tenant migration job : '.$this->tenant->name; //optional
        $params['data'] = $data;

        $this->sendEmail($params);
    }
}
