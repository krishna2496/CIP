<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Rules\CustomValidationRules;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        CustomValidationRules::validate();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191); //NEW: Increase StringLength
    }
}
