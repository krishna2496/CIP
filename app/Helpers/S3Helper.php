<?php
namespace App\Helpers;

use App\Jobs\UploadAssetsFromLocalToS3StorageJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Leafo\ScssPhp\Compiler;
use App\Helpers\ResponseHelper;
use App;

class S3Helper
{
    /**
     * Compiled local scss file and generate style.css file
     *
     * @param string $tenantName
     * @param array $options
     * @return mix
     */
    public static function compileLocalScss(string $tenantName, array $options = [])
    {
        try {
            $scss = new Compiler();
            $scss->addImportPath(realpath(storage_path().'\app\\'.$tenantName.'\assets\scss'));

            $importScss = '@import "_variables";';
            
            // Color set & other file || Color set & no file
            if ((isset($options['primary_color']) && $options['isVariableScss'] == 0)) {
                $importScss .= '$primary: '.$options['primary_color'].';';
            }

            if (file_exists(base_path()."/node_modules/bootstrap/scss/bootstrap.scss") && file_exists(base_path()."/node_modules/bootstrap-vue/src/index.js")) {
                // Send error like bootstrap.scss not found while compile files
            }

            $importScss .= '@import "custom";
            @import "../../../../node_modules/bootstrap/scss/bootstrap";
            @import "../../../../node_modules/bootstrap-vue/src/index";';

            $css = $scss->compile($importScss);
            
            // Delete if folder is already there
            if (Storage::disk('local')->exists($tenantName.'\assets\css\style.css')) {
                // Delete existing one
                Storage::disk('local')->delete($tenantName.'\assets\css\style.css');
            }

            // Put compiled css file into local storage
            if (Storage::disk('local')->put($tenantName.'\assets\css\style.css', $css)) {
                // Copy default theme folder to tenant folder on s3
                dispatch(new UploadAssetsFromLocalToS3StorageJob($tenantName));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        // Set response data
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = trans('api_success_messages.success.CSS_COMPILED_SUCESSFULLY');

        return ResponseHelper::success($apiStatus, $apiMessage);
    }

    /**
     * Upload file on AWS s3 bucket
     *
     * @param string $url
     * @param string $tenantName
     *
     * @return string
     */
    public static function uploadFileOnS3Bucket(string $url, string $tenantName)
    {
        try {
            $disk = Storage::disk('s3');
            // Comment $context_array and $context code before going live
            $context_array = array('http'=>array('proxy'=>env('AWS_WEBPROXY_HOST').":".env('AWS_WEBPROXY_PORT'),'request_fulluri'=>true));
            $context = stream_context_create($context_array);
            // Comment below line before going live
            $disk->put($tenantName.'/'.basename($url), file_get_contents($url, false, $context));
            // Uncomment below line before going live
            if ($disk->put($tenantName.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').'/'.env('AWS_S3_IMAGES_FOLDER_NAME').'/'.basename($url), file_get_contents($url))) {
                $file = $disk->get($tenantName.'/'.basename($url));
                $pathInS3 = 'https://'.env('AWS_S3_BUCKET_NAME').'.s3.'.env("AWS_REGION").'.amazonaws.com/'.$tenantName.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').'/'.env('AWS_S3_IMAGES_FOLDER_NAME').'/'.basename($url);
                return $pathInS3;
            } else {
                return 0;
            }
        } catch (\Exception $e) {            
            throw new \Exception($e->getMessage());
        }
    }
}
