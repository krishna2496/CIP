<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use App\Mail\AppMailer;

class AppMailerJob extends Job
{
    /**
     * Array for email settings
     *
     * @var array
     */
    public $params;

    /**
     * Create a new job instance.
     *
     * @param array $params
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->params['to'])->send(new AppMailer($this->params));
    }
}
