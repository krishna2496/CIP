<?php
namespace App\Rules;

use Illuminate\Support\Facades\Validator;
use App\Models\Skill;

class CustomValidationRules
{
    public static function validate()
    {
        Validator::extend('valid_media_path', function ($attribute, $value) {
            try {
                $urlMimeType = isset(get_headers($value, 1)['Content-Type']) ? get_headers($value, 1)['Content-Type'] :
                get_headers($value, 1)['content-type'];
                $validMimeTypes = config('constants.slider_image_mime_types');
                return (!in_array($urlMimeType, $validMimeTypes)) ? false : true;
            } catch (\Exception $e) {
                return false;
            }
        });

        Validator::extend('valid_document_path', function ($attribute, $value) {
            try {
                $urlMimeType = isset(get_headers($value, 1)['Content-Type']) ? get_headers($value, 1)['Content-Type'] :
                get_headers($value, 1)['content-type'];
                $validMimeTypes = config('constants.document_mime_types');
                return (!in_array($urlMimeType, $validMimeTypes)) ? false : true;
            } catch (\Exception $e) {
                return false;
            }
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

        Validator::extend('valid_timesheet_document_type', function ($attribute, $value) {
            $extension = $value->getClientOriginalExtension();
            $urlExtension = strtolower($extension);
            return (!in_array($urlExtension, config('constants.timesheet_document_types'))) ? false : true;
        });

        Validator::extend('valid_story_image_type', function ($attribute, $value) {
            $urlExtension = $value->getClientOriginalExtension();
            $imageUrlExtension = strtolower($urlExtension);
            return (!in_array($imageUrlExtension, config('constants.story_image_types'))) ? false : true;
        });
        
        Validator::extend('valid_story_video_url', function ($attribute, $value) {
            $storyVideos = explode(",", $value);
            $val = true;
            for ($i=0; $i < count($storyVideos); $i++) {
                $val = (preg_match(
                    '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11}) ~x',
                    $storyVideos[$i]
                )) ? true : false;

                if (!$val) {
                    return false;
                }
            }
            return $val;
        });

        Validator::extend('max_video_url', function ($attribute, $value) {
            $storyVideos = explode(",", $value);
            if (count($storyVideos) > config('constants.STORY_MAX_VIDEO_LIMIT')) {
                return false;
            }
            return true;
        });
    }
}
