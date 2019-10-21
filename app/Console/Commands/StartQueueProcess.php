<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\models\Job;

class StartQueueProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:queue-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will run queue for created tenant';

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

        $jobs = Job::all();
        Log::info($jobs);
        exit;
        $queueJobs = '';
        foreach ($jobs as $key => $value) {
            $queueJobs .= $value.',';
        }
        $queueJobs = trim($queueJobs, ',');
        // Log::info($queueJobs);
        $queue = 'queue:listen --queue='.$queueJobs.' --timeout=0';
            Log::info($queue);
            Artisan::call($queue);
            Log::info('Job completed');
    }
}
