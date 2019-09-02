<?php

namespace App\Jobs;

class UpdateStyleSettingsJob extends Job
{
    /**
     * @var string $tenantName
     */
    private $tenantName;

    /**
     * @var array $options
     */
    private $options;

    /**
     * @var string $fileName
     */
    private $fileName;

    /**
     * Create a new job instance.
     *
     * @param string $tenantName
     * @param array $options
     * @param string $fileName
     *
     * @return void
     */
    public function __construct(string $tenantName, array $options, string $fileName = '')
    {
        $this->tenantName = $tenantName;
        $this->options = $options;
        $this->fileName = $fileName;
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
