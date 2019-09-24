<?php

namespace App\Jobs;

use Leafo\ScssPhp\Exception\ParserException;
use App\Exceptions\FileDownloadException;
use Aws\S3\Exception\S3Exception;
use App\Exceptions\FileNotFoundException;
use Leafo\ScssPhp\Compiler;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class CompileScssFiles extends Job
{
    use RestExceptionHandlerTrait;

    /**
     * @var String
     */
    private $tenantName;

    /**
     * @var string
     */
    private $emailMessage;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     * @param String $tenantName
     * @return void
     */
    public function __construct(String $tenantName)
    {
        $this->tenantName = $tenantName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Job will try to attempt only one time. If need to re-attempt then it will delete job from table
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path().'/app/'.$this->tenantName.'/assets/scss'));

        $assetUrl = 'https://'.env("AWS_S3_BUCKET_NAME").'.s3.'
        .env("AWS_REGION", "eu-central-1").'.amazonaws.com/'.$this->tenantName.'/assets/images';
        
        try {
            $importScss =
            '@import "_assets";
            $assetUrl: "'.$assetUrl.'";
            @import "_variables";
            @import "../../../../../node_modules/bootstrap/scss/bootstrap";
            @import "../../../../../node_modules/bootstrap-vue/src/index";
            @import "custom";';

            $css = $scss->compile($importScss);
        
            // Put compiled css file into local storage
            if (Storage::disk('local')->put($this->tenantName.'\assets\css\style.css', $css)) {
                // Copy default theme folder to tenant folder on s3
                try {
                    Storage::disk('s3')->put(
                        $this->tenantName.'/assets/css/style.css',
                        Storage::disk('local')->get($this->tenantName.'\assets\css\style.css')
                    );
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
            throw $e;
        }
    }
}
