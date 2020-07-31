<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Tenant\TenantRepository;
use App\Models\Tenant;

class CopyNonExistingIconFromS3bucketToTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:new-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy non-existing icons in default theme s3 bucket to all existing tenants buckets.';

    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;

    /**
     * @var App\Models\Tenant
     */
    private $tenant;

    /**
     * Create a new command instance.
     *
     * @param App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(TenantRepository $tenantRepository, Tenant $tenant)
    {
        parent::__construct();
        $this->tenantRepository = $tenantRepository;
        $this->tenant = $tenant;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tenants = $this->tenantRepository->getAllTenants();
        $bar = $this->output->createProgressBar($tenants->count());
        if ($tenants->count() > 0) {
            dd($this->tenant->name);
            $this->info("Total tenants : ". $tenants->count());
            $this->info("\nIt is going to save new icons into tenant\n");
            $bar->start();
            foreach ($tenants as $tenant) {
            }
        }
    }
}
