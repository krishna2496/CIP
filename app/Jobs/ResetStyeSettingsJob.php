<?php

namespace App\Jobs;

class ResetStyeSettingsJob extends Job
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
    }
}
