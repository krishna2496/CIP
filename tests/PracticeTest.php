<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant;

class PracticeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $tenant = Tenant::get()->random();
        
        
        Config::shouldReceive('set')
        ->once()
        ->with(
            'database.connections.tenant',
            array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenant->tenant_id,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD')
            )
        );
        DB::shouldReceive('setDefaultConnection')->once()->with('tenant');
        DB::shouldReceive('connection')->once()->with('getDatabaseName');
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
        // Set default connection with newly created database
        DB::setDefaultConnection('tenant');
        dd(DB::connection()->getDatabaseName());
        //DB::connection('tenant')->getPdo();
        
        // return true;
    }
}
