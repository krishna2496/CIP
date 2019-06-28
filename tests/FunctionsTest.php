<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\DatabaseHelper;
use Illuminate\Support\Str;
use App\Models\Tenant;

class FunctionsTest extends TestCase
{
    /**
     * Database connection testing
     * @test
     * @return bool
     */
    public function it_should_test_tenant_database_connection()
    {
        $tenant = Tenant::get()->first();

        // Set configuration options for the newly create tenant
        Config::set(
            'database.connections.tenant',
            array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenant->tenant_id,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            )
        );
        DB::setDefaultConnection('tenant');
        DB::connection('tenant')->getPdo();

        $this->assertSame('ci_tenant_'.$tenant->tenant_id, DB::connection()->getDatabaseName());        
    }

    /**
     * Database connection testing
     * 
     * @test
     * @return bool
     */
    public function it_should_create_directory_on_s3_for_tenant_assets()
    {
        // Create folder on S3 using tenant's FQDN
        $tenantName = "testing_".Str::random(5).'_'.rand(10000,2000000);

        Storage::disk('s3')->makeDirectory($tenantName);

        // Copy default_theme folder which is already present on S3
        if (Storage::disk('s3')->exists(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'))) {
            $files = Storage::disk('s3')->allFiles(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'));

            // Fetched files copy to created s3 folder
            foreach ($files as $key => $file) {
                $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);

                // Delete if folder already exists
                if (Storage::disk('s3')->exists($tenantName.'/'.$sourcePath)) {
                    Storage::disk('s3')->delete($tenantName.'/'.$sourcePath);
                }
            
                // Copy and paste file into tenant's folders
                Storage::disk('s3')->copy($file, $tenantName.'/'.$sourcePath);
            }
        }
        $this->assertTrue(Storage::disk('s3')->exists($tenantName), $tenantName);
    }

    /**
     * Tenant's database migration testing
     * 
     * @test
     * @return void
     */
    public function it_should_create_database_and_migrations()
    {
        // Create database
        $tenant = factory(Tenant::class)->create();
        $databaseName = "ci_tenant_".$tenant->tenant_id;

        $this->assertTrue(DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`"));

        // Connect with newly created database
        $this->assertTrue(DatabaseHelper::connectWithTenantDatabase($tenant->tenant_id));
        
        $this->assertSame($databaseName, DB::connection()->getDatabaseName());

        // Call artisan command to create table for newly created tenant's database
        Artisan::call('migrate --path=database/migrations/tenant');
        
        // Disconnect and reconnect with default database
        DB::disconnect('tenant');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $this->assertSame(env('DB_MASTER'), DB::connection()->getDatabaseName());

        $this->assertTrue($tenant->delete());
    }
}
