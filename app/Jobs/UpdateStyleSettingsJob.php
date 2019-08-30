<?php

namespace App\Jobs;

use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Leafo\ScssPhp\Compiler;

class UpdateStyleSettingsJob extends Job
{
    /**
     * @var string $tenantName
     */
    private $tenantName;

    /**
     * @var array $options
     */
    private $options;

    /**
     * @var string $fileName
     */
    private $fileName;

    /**
     * Create a new job instance.
     * 
     * @param string $tenantName
     * @param array $options
     * @param string $fileName
     * 
     * @return void
     */
    public function __construct(string $tenantName, array $options, string $fileName = '')
    {
        $this->tenantName = $tenantName;
        $this->options = $options;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // First need to check is scss files folder available in local or not?
        // Need to check local copy for tenant assest is there or not?        
        // If yes then skip download files from s3 to local
        if (Storage::disk('local')->exists($this->tenantName) && !empty($this->fileName)) {
            // But need to donwload only one file that is user uploaded
            $file = $this->tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').
            '/'.config('constants.AWS_S3_SCSS_FOLDER_NAME').'/'.$this->fileName;
            Log::info('File name : '. $file);
            Storage::disk('local')->put($file, Storage::disk('s3')->get($file));
        } 
        if (!Storage::disk('local')->exists($this->tenantName)) { // Else download files from S3 to local
            // Copy files from S3 and download in local storage using tenant FQDN
            Log::info('downloading from S3');

            // Change queue default driver to database
            $queueManager = app('queue');
            $defaultDriver = $queueManager->getDefaultDriver();
            $queueManager->setDefaultDriver('sync');

            // Create new job that will take tenantName, options, and uploaded file path as an argument.
            // Dispatch job, that will store in master database
            dispatch(new DownloadAssestFromS3ToLocalStorageJob($this->tenantName));
            Log::info('files downloaded from S3');

            // Change queue driver to default
            $queueManager->setDefaultDriver($defaultDriver);

            
        }

        // Second compile SCSS files and upload generated CSS file on S3
        $this->compileLocalScss();
        Log::info('compiled successfully');
    }

    /**
     * Compiled local scss file and generate style.css file
     *
     * @param string $tenantName
     * @param array $options
     * @return mix
     */
    public function compileLocalScss()
    {
        Log::info('start compiling....');
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path().'/app/'.$this->tenantName.'/assets/scss'));
        
        $assetUrl = 'https://'.env("AWS_S3_BUCKET_NAME").'.s3.'
        .env("AWS_REGION", "eu-central-1").'.amazonaws.com/'.$this->tenantName.'/assets/images';

        $importScss = '@import "_variables";';
        
        // Color set & other file || Color set & no file
        if ((isset($this->options['primary_color']) && $this->options['isVariableScss'] == 0)) {
            $importScss .= '$primary: '.$this->options['primary_color'].';';
        }

        if (!file_exists(base_path()."/node_modules/bootstrap/scss/bootstrap.scss")
            || !file_exists(base_path()."/node_modules/bootstrap-vue/src/index.js")) {
            // Send error like bootstrap.scss not found while compile files
            /* throw new FileNotFoundException(
                trans('messages.custom_error_message.ERROR_BOOSTRAP_SCSS_NOT_FOUND'),
                config('constants.error_codes.ERROR_BOOSTRAP_SCSS_NOT_FOUND')
            ); */
            Log::info('boostrap js files not found');
        }

        try {
            $importScss .= '@import "_assets";
            $assetUrl: "'.$assetUrl.'";                        
            @import "../../../../../node_modules/bootstrap/scss/bootstrap";
            @import "../../../../../node_modules/bootstrap-vue/src/index";
            @import "custom";';

            $css = $scss->compile($importScss);
        
            // Delete if folder is already there
            if (Storage::disk('local')->exists($this->tenantName.'\assets\css\style.css')) {
                // Delete existing one
                Storage::disk('local')->delete($this->tenantName.'\assets\css\style.css');
            }

            // Put compiled css file into local storage
            if (Storage::disk('local')->put($this->tenantName.'\assets\css\style.css', $css)) {
                // Copy default theme folder to tenant folder on s3
                //try {
                    // Upload CSS file on S3 server
                    Log::info('uploading file on s3');
                    Storage::disk('s3')->put($this->tenantName.'\assets\css\style.css', Storage::disk('local')->get($this->tenantName.'\assets\css\style.css'));
                    Log::info('uploaded file on s3');
                    // dispatch(new UploadAssetsFromLocalToS3StorageJob($this->tenantName));
                /* } catch (S3Exception $e) {
                    return $this->s3Exception(
                        config('constants.error_codes.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3'),
                        trans('messages.custom_error_message.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3')
                    );
                } */
            } else {
                /* throw new FileDownloadException(
                    trans('messages.custom_error_message.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL'),
                    config('constants.error_codes.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL')
                ); */
            }
        } /* catch (ParserException $e) {
            throw new ParserException(
                trans('messages.custom_error_message.ERROR_WHILE_COMPILING_SCSS_FILES'),
                config('constants.error_codes.ERROR_WHILE_COMPILING_SCSS_FILES')
            );
        } */ catch (\Exception $e) {
            //return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }

        /* // Set response data
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = trans('api_success_messages.success.CSS_COMPILED_SUCESSFULLY');

        return $this->responseHelper->success($apiStatus, $apiMessage); */
    }
}
