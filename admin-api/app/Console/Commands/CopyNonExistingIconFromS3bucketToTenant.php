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
    protected $signature = 'copy:new-icons {--folder=} {--file=}';

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
        // --path option is available
        $folderPath = $this->option('folder');

        // --file option is available
        $filePath = $this->option('file');

        if (!empty($folderPath) && empty($filePath)) {

            //check --path option directory is exist or not
            if (Storage::disk('s3')->exists($folderPath)) {
                $files = Storage::disk('s3')->allFiles($folderPath);
                $tenants = $this->tenantRepository->getAllTenants();
                $option = 'folder';

                // Function for copy non existing icons 
                $this->copyNonExistingIconPerTenant($tenants, $files, $option);
            } else {
                dump("Given folder path is not found");
            }
        } elseif (!empty($filePath) && empty($folderPath)) {

            // Check filepath is exist or not
            if (Storage::disk('s3')->exists($filePath)) {
                $encodedFileContent = Storage::disk('s3')->get($filePath);
                $fileContent = json_decode($encodedFileContent);
                $tenants = $this->tenantRepository->getAllTenants();
                $option = 'file';

                // Function for copy non existing icons
                $this->copyNonExistingIconPerTenant($tenants, $fileContent->files, $option);
            } else {
                dump("Given filepath is not found.");
            }
        } else {
            if (!empty($filePath) && !empty($folderPath)) {
                dump('Only single option is acceptable at a time');
            } else {
                dump('path or file option is missing');
            }
        }
    }

    private function copyNonExistingIconPerTenant($tenants, $files, $option)
    {
        if ($tenants->count() > 0) {
            $bar = $this->output->createProgressBar($tenants->count());
            $tenantsList = $tenants->toArray();
            $this->info('Total tenants : '. $tenants->count());
            $this->info("\nIt is going to save new icons into tenant\n");
            $bar->start();
            $notAvailableImagesArray = [];
            foreach ($tenantsList as $tenant) {

                // Check tenant directory is exist or not
                if (Storage::disk('s3')->exists('testCommand25')) {
                    foreach ($files as $file) {
                        // Remove default_theme path from file URL
                        $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);
                        if (!Storage::disk('s3')->exists('testCommand25'.$sourcePath)) {
                            array_push($notAvailableImagesArray, $file);

                            // Copy and paste file into tenant's folders
                            Storage::disk('s3')->copy($file, 'testCommand25'.$sourcePath);
                        }
                    }
                }

                $bar->advance();
            }

            dd("finish");
            $bar->finish();
            $this->info("\n \nAll non existing icons are copied from default_theme to tenant");
        }
    }
}
