<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use App\Exceptions\FileDownloadException;

class DownloadAssestFromS3ToLocalStorageJob extends Job
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
        $sourceFolder = storage_path('app/'.config('constants.AWS_S3_DEFAULT_THEME_FOLDER_NAME'));
        $destinationFolder = storage_path('app/'.$this->tenantName);

        mkdir($destinationFolder);
        exec('cp -r '.$sourceFolder.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').' '.$destinationFolder.' ');
    }
}
