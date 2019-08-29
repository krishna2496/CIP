<?php

namespace App\Jobs;

use App\Jobs\CreateFolderInS3BucketJob;
use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use App\Jobs\CompileScssFiles;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class ResetStyeSettingsJob extends Job
{
    /**
     * @var App\Models\Tenant
     */
    private $tenant;

    /**
     * @var string $tenantName
     */
    private $tenantName;

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
     * @param string $tenantName
     * @return void
     */
    public function __construct(string $tenantName)
    {
        $this->tenantName = $tenantName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->tenant = Tenant::where('name', $this->tenantName)->whereNull('deleted_at')->first();
        
        // Change queue default driver to database
        $queueManager = app('queue');
        $defaultDriver = $queueManager->getDefaultDriver();
        $queueManager->setDefaultDriver('sync');

        // Copy default theme folder to tenant folder on s3
        dispatch(new CopySCSSFolderInS3BucketJob($this->tenant));

        // Copy tenant folder to local
        dispatch(new DownloadAssestFromS3ToLocalStorageJob($this->tenant->name));
        
        // Compile downloaded files and update css on s3
        dispatch(new CompileScssFiles($this->tenant));

        // Change queue driver to default
        $queueManager->setDefaultDriver($defaultDriver);
    }
}
