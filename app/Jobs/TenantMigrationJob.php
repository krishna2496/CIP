<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Helpers\DatabaseHelper;
use App\Models\Tenant;
use DB;
use App\Helpers\EmailHelper;

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
     *
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
        // Create database
        DB::statement("CREATE DATABASE IF NOT EXISTS `ci_tenant_{$this->tenant->tenant_id}` 
        DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Connect with newly created database
        $this->databaseHelper->connectWithTenantDatabase($this->tenant->tenant_id);

        // Call artisan command to create table for newly created tenant's database
        Artisan::call('migrate --path=database/migrations/tenant');

        // Call artisan command to run database seeder for default values
        Artisan::call('db:seed');

        // Fetch uploaded files from seeder folder and run one by one
        $seederFiles = Storage::disk('seeder')->allFiles();
        
        foreach ($seederFiles as $file) {
            $seederClassName = explode(".", $file)[0];
            Artisan::call("db:seed --class=$seederClassName");
            DB::table('seeders')->insert([
                'seeder' => $file
            ]);
        }
        
        
        // Disconnect and reconnect with default database
        DB::disconnect('tenant');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');
    }
}
