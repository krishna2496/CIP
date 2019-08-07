<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('valid_media_path', function ($attribute, $value, $parameters) {
            $urlExtension = pathinfo($value, PATHINFO_EXTENSION);
            return (!in_array($urlExtension, config('constants.image_types'))) ? false : true;
        });

        Validator::extend('valid_document_path', function ($attribute, $value, $parameters) {
            $urlExtension = pathinfo($value, PATHINFO_EXTENSION);
            return (!in_array($urlExtension, config('constants.document_types'))) ? false : true;
        });
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
