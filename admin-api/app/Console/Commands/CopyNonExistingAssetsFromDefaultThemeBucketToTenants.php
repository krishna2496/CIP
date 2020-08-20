<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Support\Facades\Storage;

class CopyNonExistingAssetsFromDefaultThemeBucketToTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:copy {--folder=} {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy non-existing assets in default theme s3 bucket to all existing tenants buckets.';

    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;

    /**
     * Create a new command instance.
     *
     * @param App\Repositories\Tenant\TenantRepository $tenantRepository
     * @return void
     */
    public function __construct(TenantRepository $tenantRepository)
    {
        parent::__construct();
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // --folder option is available
        $folderPath = $this->option('folder');

        // --file option is available
        $filePath = $this->option('file');
        $defaultThemePath = 'default_theme/';

        if (!empty($folderPath) && empty($filePath)) {

            // Check --path option directory is exist or not
            if (Storage::disk('s3')->exists($defaultThemePath.$folderPath)) {
                $files = Storage::disk('s3')->allFiles($folderPath);
                $tenants = $this->tenantRepository->getAllTenants();

                // Function for copy non existing icons
                $this->copyNonExistingIconPerTenant($tenants, $files);
            } else {
                $this->warn('Given folder path is not found');
            }
        } elseif (!empty($filePath) && empty($folderPath)) {

            // Check filepath is exist or not
            if (Storage::disk('s3')->exists($defaultThemePath.$filePath)) {
                $encodedFileContent = Storage::disk('s3')->get($filePath);
                $fileContent = json_decode($encodedFileContent);
                $tenants = $this->tenantRepository->getAllTenants();

                // Function for copy non existing icons
                $this->copyNonExistingIconPerTenant($tenants, $fileContent->files);
            } else {
                $this->warn('Given filepath is not found');
            }
        } else {
            if (!empty($filePath) && !empty($folderPath)) {
                $this->warn('Only single option is acceptable at a time');
            } else {
                $this->warn('Folder or file option is missing');
            }
        }
    }

    /**
     * Copy non existing files
     *
     * @param null|Illuminate\Support\Collection $tenants
     * @param array $files
     * @return void
     */
    private function copyNonExistingIconPerTenant($tenants, array $files)
    {
        dd($tenants);
        if ($tenants->count() > 0) {
            $bar = $this->output->createProgressBar($tenants->count());
            $tenantsList = $tenants->toArray();
            $this->info('Total tenants : '. $tenants->count());
            $bar->start();
            foreach ($tenantsList as $tenant) {

                // Check tenant directory is exist or not
                if (Storage::disk('s3')->exists($tenant['name'])) {
                    foreach ($files as $file) {
                        
                        // Remove default_theme path from file URL
                        $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);
                        if (!Storage::disk('s3')->exists($tenant['name'].$sourcePath)) {

                            // Copy and paste file into tenant's folders
                            Storage::disk('s3')->copy($file, $tenant['name'].$sourcePath);
                        }
                    }
                }
                $bar->advance();
            }
            $bar->finish();
            $this->info("\n \nAll non existing icons are copied from default_theme to tenant");
        }
    }
}
