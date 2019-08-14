<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('valid_media_path', function ($attribute, $value) {
            $urlExtension = pathinfo($value, PATHINFO_EXTENSION);
            return (!in_array($urlExtension, config('constants.image_types'))) ? false : true;
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
		
		Validator::extend('valid_profile_image',function($attribute, $value, $params, $validator) {
			$image = base64_decode($value);
			$f = finfo_open();
			$result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
			return in_array($result, config('constants.profile_image_types'));
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
