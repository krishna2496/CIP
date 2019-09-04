<?php

namespace App\Repositories\MissionSkill;

use Illuminate\Support\ServiceProvider;

class MissionSkillRepoServiceProvide extends ServiceProvider
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
            'App\Repositories\MissionSkill\MissionSkillInterface',
            'App\Repositories\MissionSkill\MissionSkillRepository'
        );
    }
}
