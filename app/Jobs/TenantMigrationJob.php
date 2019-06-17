<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
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
     * @param App\Tenant $tenant
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
        try {
			// Create database
			DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$this->tenant->tenant_id}`");

			// Set configuration options for the newly create tenant
			Config::set('database.connections.tenant', array(
				'driver'    => 'mysql',
				'host'      => env('DB_HOST'),
				'database'  => 'ci_tenant_'.$this->tenant->tenant_id,
				'username'  => env('DB_USERNAME'),
				'password'  => env('DB_PASSWORD'),
			));

			// Set default connection with newly created database
			DB::setDefaultConnection('tenant');
			$pdo = DB::connection('tenant')->getPdo();
			
			// Call artisan command to create table for newly created tenant's database
			Artisan::call('migrate --path=database/migrations/tenant');
			
			// Disconnect and reconnect with default database
			DB::disconnect('tenant');
			DB::reconnect('mysql');
			DB::setDefaultConnection('mysql');
			
		} catch (\Exception $e) {
			
			throw new \Exception($e->getMessage());
			
		}
    }
}
