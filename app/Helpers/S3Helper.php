<?php
namespace App\Helpers;

use App\Jobs\UploadAssetsFromLocalToS3StorageJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Leafo\ScssPhp\Compiler;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use App;
use DB;
use Leafo\ScssPhp\Exception\ParserException;
use App\Exceptions\FileDownloadException;
use App\Exceptions\BucketNotFoundException;
use Aws\S3\Exception\S3Exception;
use App\Exceptions\FileNotFoundException;

class S3Helper
{
    use RestExceptionHandlerTrait;
    /**
     * Create a new middleware instance.
     *
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    /**
     * Compiled local scss file and generate style.css file
     *
     * @param string $tenantName
     * @param array $options
     * @return mix
     */
    public function compileLocalScss(string $tenantName, array $options = [])
    {
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path().'/app/'.$tenantName.'/assets/scss'));
        
        $assetUrl = 'https://'.env("AWS_S3_BUCKET_NAME").'.s3.'
        .env("AWS_REGION", "eu-central-1").'.amazonaws.com/'.$tenantName.'/assets/images';

        $importScss = '@import "_variables";';
        
        // Color set & other file || Color set & no file
        if ((isset($options['primary_color']) && $options['isVariableScss'] == 0)) {
            $importScss .= '$primary: '.$options['primary_color'].';';
        }

        if (!file_exists(base_path()."/node_modules/bootstrap/scss/bootstrap.scss")
            || !file_exists(base_path()."/node_modules/bootstrap-vue/src/index.js")) {
            // Send error like bootstrap.scss not found while compile files
            throw new FileNotFoundException(
                trans('messages.custom_error_message.ERROR_BOOSTRAP_SCSS_NOT_FOUND'),
                config('constants.error_codes.ERROR_BOOSTRAP_SCSS_NOT_FOUND')
            );
        }

        try {
            $importScss .= '@import "_assets";
            $assetUrl: "'.$assetUrl.'";                        
            @import "../../../../../node_modules/bootstrap/scss/bootstrap";
            @import "../../../../../node_modules/bootstrap-vue/src/index";
            @import "custom";';

            $css = $scss->compile($importScss);
        
            // Delete if folder is already there
            if (Storage::disk('local')->exists($tenantName.'\assets\css\style.css')) {
                // Delete existing one
                Storage::disk('local')->delete($tenantName.'\assets\css\style.css');
            }

            // Put compiled css file into local storage
            if (Storage::disk('local')->put($tenantName.'\assets\css\style.css', $css)) {
                // Copy default theme folder to tenant folder on s3
                try {
                    dispatch(new UploadAssetsFromLocalToS3StorageJob($tenantName));
                } catch (S3Exception $e) {
                    return $this->s3Exception(
                        config('constants.error_codes.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3'),
                        trans('messages.custom_error_message.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3')
                    );
                }
            } else {
                throw new FileDownloadException(
                    trans('messages.custom_error_message.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL'),
                    config('constants.error_codes.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL')
                );
            }
        } catch (ParserException $e) {
            throw new ParserException(
                trans('messages.custom_error_message.ERROR_WHILE_COMPILING_SCSS_FILES'),
                config('constants.error_codes.ERROR_WHILE_COMPILING_SCSS_FILES')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }

        // Set response data
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = trans('api_success_messages.success.CSS_COMPILED_SUCESSFULLY');

        return $this->responseHelper->success($apiStatus, $apiMessage);
    }

    /**
     * Upload file on AWS s3 bucket
     *
     * @param string $url
     * @param string $tenantName
     *
     * @return string
     */
    public function uploadFileOnS3Bucket(string $url, string $tenantName): string
    {
        $disk = Storage::disk('s3');
        // Comment $context_array and $context code before going live
        $context_array = array('http'=>array('proxy'=>env('AWS_WEBPROXY_HOST').":".env('AWS_WEBPROXY_PORT'),
        'request_fulluri'=>true));
        $context = stream_context_create($context_array);
        // Comment below line before going live
        $disk->put($tenantName.'/'.basename($url), file_get_contents($url, false, $context));
        // Uncomment below line before going live
        if ($disk->put(
            $tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').'/'
            .config('constants.AWS_S3_IMAGES_FOLDER_NAME')
            .'/'.basename($url),
            file_get_contents($url)
        )) {
            $file = $disk->get($tenantName.'/'.basename($url));
            $pathInS3 = 'https://'.env('AWS_S3_BUCKET_NAME').'.s3.'
            .env("AWS_REGION").'.amazonaws.com/'.$tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME')
            .'/'.config('constants.AWS_S3_IMAGES_FOLDER_NAME').'/'.basename($url);
            return $pathInS3;
        } else {
            return 0;
        }
    }

    /**
     * Get all SCSS files list from S3 bucket
     *
     * @param string $tenantName
     */
    public function getAllScssFiles(string $tenantName)
    {
        if (Storage::disk('s3')->exists($tenantName)) {
            $allFiles = Storage::disk('s3')->allFiles($tenantName);
            $scssFilesArray = [];
            $i = $j = 0;

            if (count($allFiles) > 0) {
                foreach ($allFiles as $key => $file) {
                    // Only scss and css copy
                    if (!strpos($file, "/images") && strpos($file, "/scss")
                    && !strpos($file, "custom.scss") && !strpos($file, "assets.scss")) {
                        $scssFilesArray['scss_files'][$i++] = [
                            "scss_file_path" =>
                            'https://s3.' . env('AWS_REGION') . '.amazonaws.com/'.env('AWS_S3_BUCKET_NAME').'/'.$file,
                            "scss_file_name" => basename($file)
                        ];
                    }
                    if (strpos($file, "/images") && !strpos($file, "/scss")
                    && !strpos($file, "custom.scss") && !strpos($file, "assets.scss")) {
                        $scssFilesArray['image_files'][$j++] = [
                            "image_file_path" =>
                            'https://s3.' . env('AWS_REGION') . '.amazonaws.com/' . env('AWS_S3_BUCKET_NAME')
                            . '/'.$file,
                            "image_file_name" => basename($file)
                        ];
                    }
                }
            }
        } else {
            throw new BucketNotFoundException(
                trans('messages.custom_error_message.ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3'),
                config('constants.error_codes.ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3')
            );
        }
        return $scssFilesArray;
    }

    /**
     * Upload profile image on AWS s3 bucket
     *
     * @param string $avatar
     * @param string $tenantName
     * @param int $userId
     *
     * @return string
     */
    public function uploadProfileImageOnS3Bucket(string $avatar, string $tenantName, int $userId): string
    {
        try {
            // Get file type from base64
            $fileOpen = finfo_open();
            $mime_type = finfo_buffer($fileOpen, base64_decode($avatar), FILEINFO_MIME_TYPE);

            $type = explode('/', $mime_type);
            
            $imagePath = $tenantName.'/profile_images/'.$userId.'_'.time().'.'.$type[1];
            Storage::disk('s3')->put($imagePath, base64_decode($avatar), 'public');
            $filePath =  Storage::disk('s3')->url($imagePath);
            return $filePath;
        } catch (S3Exception $e) {
            return $this->s3Exception(
                config('constants.error_codes.ERROR_FAILD_TO_UPLOAD_PROFILE_IMAGE_ON_S3'),
                trans('messages.custom_error_message.ERROR_FAILD_TO_UPLOAD_PROFILE_IMAGE_ON_S3')
            );
        }
    }
}
