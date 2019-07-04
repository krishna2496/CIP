<?php
namespace App\Jobs;

use App;
use App\Models\TenantOption;
use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;

class CreateFolderInS3BucketJob extends Job
{
    protected $tenantName;

    /**
     * Create a new job instance.
     *
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
        // Copy default theme folder
        if (Storage::disk('s3')->exists(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'))) {
            $allFiles = Storage::disk('s3')->allFiles(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'));

            foreach ($allFiles as $key => $file) {
                // Only scss and css copy
                if (!strpos($file, "/images")) {
                    $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);
                    // Delete if folder is already there
                    if (Storage::disk('s3')->exists($this->tenantName . '/' . $sourcePath)) {
                        // Delete existing one
                        Storage::disk('s3')->delete($this->tenantName . '/' . $sourcePath);
                    }
                    // copy and paste file into tenant's folders
                    Storage::disk('s3')->copy($file, $this->tenantName . '/' . $sourcePath);
                    if (basename($file) == env('S3_CUSTOME_CSS_NAME')) {
                        $pathInS3 = 'https://s3.' . env('AWS_REGION') . '.amazonaws.com/'
                            . env('AWS_S3_BUCKET_NAME') . '/' . $this->tenantName . '' . $sourcePath;

                        // Create or update custom css record on database
                        $tenantOptionData['option_name'] = "custom_css";
                        $tenantOption                    = TenantOption::updateOrCreate($tenantOptionData);
                        $tenantOption->update(['option_value' => $pathInS3]);
                    }
                }
            }
        } else {
            return $this->s3Exception(
                config('constants.error_codes.DEFAULT_THEME_FOLDER_NOT_FOUND'),
                trans('messages.custom_error_message.DEFAULT_THEME_FOLDER_NOT_FOUND')
            );
        }
    }
}
