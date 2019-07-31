<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use App\Traits\RestExceptionHandlerTrait;
use App\Exceptions\FileUploadException;
use Aws\S3\Exception\S3Exception;

class UploadAssetsFromLocalToS3StorageJob extends Job
{

    /**
     * @var string $tenantName
     */
    protected $tenantName;

    /**
     * Create a new job instance.
     *
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
        $allFiles = Storage::disk('local')->allFiles($this->tenantName.'/assets');
        if (count($allFiles)) {
            foreach ($allFiles as $key => $file) {
                $sourcePath = str_replace($this->tenantName, '', $file);
                Storage::disk('s3')->put($file, Storage::disk('local')->get($file));
            }
        } else {
            throw new FileUploadException(
                trans('messages.custom_error_message.ERROR_NO_FILES_FOUND_TO_UPLOAD_ON_S3_BUCKET'),
                config('constants.error_codes.ERROR_NO_FILES_FOUND_TO_UPLOAD_ON_S3_BUCKET')
            );
        }
    }
}
