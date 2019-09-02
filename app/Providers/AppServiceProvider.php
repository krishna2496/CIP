<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Validator;
use App\Models\Skill;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('valid_media_path', function ($attribute, $value) {
            $urlExtension = pathinfo($value, PATHINFO_EXTENSION);
            $validExtensions = ($attribute == 'url') ?
            config('constants.slider_image_types') : config('constants.image_types');
            return (!in_array($urlExtension, $validExtensions)) ? false : true;
        });

        Validator::extend('valid_document_path', function ($attribute, $value) {
            $urlExtension = pathinfo($value, PATHINFO_EXTENSION);
            return (!in_array($urlExtension, config('constants.document_types'))) ? false : true;
        });
        
        Validator::extend('valid_video_url', function ($attribute, $value) {
            return (preg_match(
                '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11}) ~x',
                $value
            ))
            ? true : false;
        });
        
        Validator::extend('valid_profile_image', function ($attribute, $value, $params, $validator) {
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $value));
            $f = finfo_open();
            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            return in_array($result, config('constants.profile_image_types'));
        });

        Validator::extend('valid_parent_skill', function ($attribute, $value) {
            return ($value == 0) ? true : ((empty(Skill::where('skill_id', $value)->get()->toArray())) ? false : true);
        });

        Validator::extend('valid_linkedin_url', function ($attribute, $value) {
            return (preg_match(
                '/(https?)?:?(\/\/)?(([w]{3}||\w\w)\.)'.
                '?linkedin.com(\w+:{0,1}\w*@)?(\S+)'.
                '(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/',
                $value
            ))
            ? true : false;
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
