<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;

class UploadAssetsFromLocalToS3StorageJob extends Job
{
    protected $tenantName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantName)
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
        try {
            $allFiles = Storage::disk('local')->allFiles($this->tenantName.'/assets');
            foreach ($allFiles as $key => $file) {
                $sourcePath = str_replace($this->tenantName, '', $file);
                Storage::disk('s3')->put($file, Storage::disk('local')->get($file));
            }
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
