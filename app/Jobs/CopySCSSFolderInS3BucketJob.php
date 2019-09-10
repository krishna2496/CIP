<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;

class CopySCSSFolderInS3BucketJob extends Job
{
    /**
     * @var String
     */
    private $tenantName;

    /**
     * Create a new job instance.
     * @param App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(String $tenantName)
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
        exec('aws s3 cp --recursive s3://'.config('constants.AWS_S3_BUCKET_NAME').
            '/'.config('constants.AWS_S3_DEFAULT_THEME_FOLDER_NAME').'/'.env('AWS_S3_ASSETS_FOLDER_NAME').'/scss s3://'
            .config('constants.AWS_S3_BUCKET_NAME').'/'
            .$this->tenantName.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').'/scss');
    }
}
