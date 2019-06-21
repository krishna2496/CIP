<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Storage;

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
        if (Storage::disk('local')->exists($this->tenantName)) {
            Storage::disk('local')->delete($this->tenantName);
        }

        Storage::disk('local')->makeDirectory($this->tenantName);

        $allFiles = Storage::disk('s3')->allFiles($this->tenantName.'/assets/scss');

        foreach ($allFiles as $key => $file) {
            $sourcePath = str_replace($this->tenantName, '', $file);
            if (Storage::disk('local')->exists($file)) {
                // Delete existing one
                Storage::disk('local')->delete($file);
            }
            Storage::disk('local')->put($file, Storage::disk('s3')->get($file));
        }
    }
}
