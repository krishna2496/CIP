<?php

namespace App\Console\Commands;

use DB;

use App\Models\Tenant;
use App\Helpers\Helpers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;


class RunAllTenantJobs extends Command
{




    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:jobs';

    protected $helpers;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "It will run all jobs from each tenant's database";

    /**
     * Create a new command instance.
     * @codeCoverageIgnore
     * 
     * @return void
     */
    public function __construct(Helpers $helpers)
    {
        parent::__construct();
        $this->helpers = $helpers;
    }

    /**
     * Execute the console command.
     * @codeCoverageIgnore
     * 
     * @return mixed
     */
    public function handle()
    {
        $this->helpers->switchDatabaseConnection('mysql');
        
        $tenants = DB::select('select * from tenant where tenant_id in (1000, 1797)');
        // dd($tenants);        
        $bar = $this->output->createProgressBar(sizeof($tenants));
        if (sizeof($tenants) > 0) {
            $this->info("Total tenants : ". sizeof($tenants));
            $this->info("\nIt is going to rollback migration changes\n");
            $bar->start();
            foreach ($tenants as $tenant) {
                // Create connection of tenant one by one
                if ($this->createConnection($tenant->tenant_id) !== 0) {
                    try {
                        $this->info(DB::connection()->getDatabaseName());
                        // Run migration command to apply migration change
                        Artisan::call('queue:work --stop-when-empty database');
                    } catch (\Exception $e) {
                        // Failed then send mail to admin
                        $this->warn("\n \n Error while listening job of :
                        $tenant->name (tenant id : $tenant->tenant_id)");
                        $this->error("\n\n".$e->getMessage());
                        continue;
                    }
                    $bar->advance();
                    // Disconnect database and connect with master DB
                    DB::disconnect('tenant');
                    DB::reconnect('mysql');
                }
            }
            $bar->finish();
            $this->info("\n \nAll jobs dispatched!");
        } else {
            $this->warn("No tenant found");
        }
    }

    /**
     * Send email notification to admin
     * @codeCoverageIgnore
     *
     * @param App\Models\Tenant $tenant
     * @param string $type
     * @return void
     */
    public function sendFailerMail(Tenant $tenant, string $type)
    {
        $message = "Seeder rollback filed for tenant : ". $tenant->name. '.';
        $params['subject'] = 'Error in migration rollback';

        $message .= "<br> Database name : ". "ci_tenant_". $tenant->tenant_id;

        $data = array(
            'message'=> $message,
            'tenant_name' => $tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'
        .config('constants.EMAIL_TEMPLATE_MIGRATION_NOTIFICATION'); //path to the email template

        $params['data'] = $data;

        // $this->emailHelper->sendEmail($params);
    }

    /**
     * Create connection with tenant's database
     * @codeCoverageIgnore
     *
     * @param int $tenantId
     * @return int
     */
    public function createConnection(int $tenantId): int
    {
        DB::purge('tenant');
        
        // Set configuration options for the newly create tenant
        Config::set(
            'database.connections.tenant',
            array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenantId,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            )
        );

        // Set default connection with newly created database
        DB::setDefaultConnection('tenant');

        try {
            DB::connection('tenant')->getPdo();
        } catch (\Exception $exception) {
            return 0;
        }

        return $tenantId;
    }
}
