<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;

class CopySCSSFolderInS3BucketJob extends Job
{
    /**
     * @var App\Models\Tenant
     */
    private $tenant;

    /**
     * Create a new job instance.
     * @param App\Models\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $allFiles = Storage::disk('s3')->allFiles(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'));

        foreach ($allFiles as $key => $file) {
            // Only scss and css copy
            if (!strpos($file, "/images")) {
                $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);
                // Delete if folder is already there
                if (Storage::disk('s3')->exists($this->tenant->name . '/' . $sourcePath)) {
                    // Delete existing one
                    Storage::disk('s3')->delete($this->tenant->name . '/' . $sourcePath);
                }
                // copy and paste file into tenant's folders
                Storage::disk('s3')->copy($file, $this->tenant->name . '/' . $sourcePath);
                if (basename($file) == config('constants.AWS_S3_CUSTOME_CSS_NAME')) {
                    $pathInS3 = 'https://s3.' . env('AWS_REGION') . '.amazonaws.com/'
                        . env('AWS_S3_BUCKET_NAME') . '/' . $this->tenant->name . '' . $sourcePath;
                }
            }
        }
    }
}
