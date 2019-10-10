<?php

namespace App\Jobs;

use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use App\Jobs\CompileScssFiles;
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
        // @codeCoverageIgnoreStart
        if (Storage::disk('local')->exists($this->tenantName) && !empty($this->fileName)) {
            // But need to donwload only one file that is user uploaded
            $file = $this->tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').
            '/'.config('constants.AWS_S3_SCSS_FOLDER_NAME').'/'.$this->fileName;
            Storage::disk('local')->put($file, Storage::disk('s3')->get($file));
        }
        if (!Storage::disk('local')->exists($this->tenantName)) { // Else download files from S3 to local
            // Create new job that will take tenantName, options, and uploaded file path as an argument.
            // Dispatch job, that will store in master database
            dispatch(new DownloadAssestFromS3ToLocalStorageJob($this->tenantName));
        }
        // @codeCoverageIgnoreEnd
        // Second compile SCSS files and upload generated CSS file on S3
        $this->compileLocalScss();
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
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path().'/app/'.$this->tenantName.'/assets/scss'));
        
        $assetUrl = 'https://'.env("AWS_S3_BUCKET_NAME").'.s3.'
        .env("AWS_REGION", "eu-central-1").'.amazonaws.com/'.$this->tenantName.'/assets/images';

        $importScss = '@import "_variables";';
        
        // Color set & other file || Color set & no file
        if ((isset($this->options['primary_color']) && $this->options['isVariableScss'] == 0)) {
            $importScss .= '$primary: '.$this->options['primary_color'].';';
        }

        $importScss .= '@import "_assets";
        $assetUrl: "'.$assetUrl.'";                        
        @import "../../../../../node_modules/bootstrap/scss/bootstrap";
        @import "../../../../../node_modules/bootstrap-vue/src/index";
        @import "custom";';

        $css = $scss->compile($importScss);
    
        // Put compiled css file into local storage
        if (Storage::disk('local')->put($this->tenantName.'\assets\css\style.css', $css)) {
            Storage::disk('s3')->put($this->tenantName.'\assets\css\style.css', Storage::disk('local')->get($this->tenantName.'\assets\css\style.css'));
        }
    }
}
