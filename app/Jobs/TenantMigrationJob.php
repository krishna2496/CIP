<?php
namespace App\Jobs;

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
     * Create a new job instance.
     *
     * @param  App\Tenant $tenant Tenant model object
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
        // Create database
        DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$this->tenant->tenant_id}`");

        // Connect with newly created database
        DatabaseHelper::connectWithTenantDatabase($this->tenant->tenant_id);
        
        // Call artisan command to create table for newly created tenant's database
        Artisan::call('migrate --path=database/migrations/tenant');
        
        // Disconnect and reconnect with default database
        DB::disconnect('tenant');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');
    }
}
