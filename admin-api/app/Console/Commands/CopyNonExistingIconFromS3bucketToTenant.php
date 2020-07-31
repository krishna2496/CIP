<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Tenant\TenantRepository;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class CopyNonExistingIconFromS3bucketToTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:new-icons {--path=}';

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
        $folderPath = $this->option('path');

        if(!empty($folderPath)){
            $tenants = $this->tenantRepository->getAllTenants();
            $bar = $this->output->createProgressBar($tenants->count());
            if ($tenants->count() > 0) {
                // $test = $tenants->toArray()[0]['name']; 
                // $available = Storage::disk('s3')->exists('/file.jpg');
                // dd($available);
                // Copy default_theme folder which is already present on S3
                $files = Storage::disk('s3')->allFiles(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME').
                '/'.env('AWS_S3_ASSETS_FOLDER_NAME'));
                $tenantsList = $tenants->toArray();
                // dd($tenantsList);
                $this->info('Total tenants : '. $tenants->count());
                $this->info("\nIt is going to save new icons into tenant\n");
                $bar->start();
                foreach ($tenantsList as $tenant) {
                    dd("test");
                    if(Storage::isDirectory(Storage::disk('s3')->allFiles($tenantsList['name']))){
                        // you code...
                    }
                    $tenantName = $tenant['name'];
                }
            }
        } else {
            dump('path option is missing');
        }

    }
}
