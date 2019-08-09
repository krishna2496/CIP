<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\DatabaseHelper;
use App\Models\Tenant;
use DB;

class TenantMigrationJob extends Job
{
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
            } catch (\Exception $e) {
                Log::info("Tenant" .$this->tenant->name."'s : Tenant migration job have some error.
                So, tenant and database deleted");

                // Delete created tenant
                $this->tenant->delete();
                // Drop tanant database
                DB::statement("DROP DATABASE IF EXISTS `ci_tenant_{$this->tenant->tenant_id}`");
            }
        } else {
            Log::info("Tenant" .$this->tenant->name."'s : Tenant migration job have some error.
            So, job deleted from database");
            // Delete job from database
            $this->delete();
        }
    }
}
