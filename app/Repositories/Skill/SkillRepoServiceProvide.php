<?php

namespace App\Repositories\Skill;

use Illuminate\Support\ServiceProvider;

class SkillRepoServiceProvide extends ServiceProvider
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
        $this->app->bind('App\Repositories\Skill\SkillInterface', 'App\Repositories\Skill\SkillRepository');
    }
}
