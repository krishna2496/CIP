<?php

namespace App\Jobs;

class ResetStyleSettingsJob extends Job
{
    /**
     * @var string $tenantName
     */
    private $tenantName;

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
        // Copy default theme folder to tenant folder on s3
        dispatch(new CopySCSSFolderInS3BucketJob($this->tenantName));

        // Copy tenant folder to local
        dispatch(new DownloadAssestFromLocalDefaultThemeToLocalStorageJob($this->tenantName));
        
        // Compile downloaded files and update css on s3
        dispatch(new CompileScssFiles($this->tenantName));
    }
}
