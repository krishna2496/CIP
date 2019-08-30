<?php

namespace App\Repositories\Timesheet;

use Illuminate\Support\ServiceProvider;

class TimesheetRepoServiceProvide extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Repositories\Timesheet\TimesheetInterface',
            'App\Repositories\Timesheet\TimesheetRepository'
        );
    }
}
