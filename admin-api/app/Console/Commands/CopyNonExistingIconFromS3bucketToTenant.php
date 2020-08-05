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
    protected $signature = 'copy:new-icons {--path=} {--file=}';

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
        $filePath = $this->option('file');

        if (!empty($folderPath) && empty($filePath)) {
            $tenants = $this->tenantRepository->getAllTenants();
            $bar = $this->output->createProgressBar($tenants->count());
            if ($tenants->count() > 0) {

                // Copy default_theme folder which is already present on S3
                $files = Storage::disk('s3')->allFiles(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME').
                '/'.env('AWS_S3_ASSETS_FOLDER_NAME'));

                $tenantsList = $tenants->toArray();
                $this->info('Total tenants : '. $tenants->count());
                $this->info("\nIt is going to save new icons into tenant\n");
                $bar->start();

                $notAvailableImagesArray = [];
                foreach ($tenantsList as $tenant) {

                    // Check tenant directory is exist or not 
                    if(Storage::disk('s3')->exists('testCommand26')){

                        //check --path option directory is exist or not
                        if(Storage::disk('s3')->exists($folderPath)){
                            $files = Storage::disk('s3')->allFiles($folderPath);
                            foreach($files as $key=>$file){

                                // Remove default_theme path from file URL
                                $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);
                                if(!Storage::disk('s3')->exists('testCommand26'.$sourcePath)){
                                    array_push($notAvailableImagesArray, $file);
                                    // dd($tenant['name'].$sourcePath);
                                    // Copy and paste file into tenant's folders
                                    Storage::disk('s3')->copy($file, 'testCommand26'.$sourcePath);
                                    // dd('testCommand26'.$sourcePath);
                                }
                            }

                            dd($notAvailableImagesArray);
                        }
                    }
                    // $tenantName = $tenant['name'];
                }
            }
        } elseif (!empty($filePath) && empty($folderPath)) {
            dump('file option is available');
        } else {
            if (!empty($filePath) && !empty($folderPath)) {
                dump('path and file both options are available');
            } else {
                dump('path or file option is missing');
            }
        }
    }
}
