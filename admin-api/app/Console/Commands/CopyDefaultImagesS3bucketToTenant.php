<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CopyDefaultImagesS3bucketToTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:new-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy non-existing icons in default theme s3 bucket to all existing tenants buckets.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
