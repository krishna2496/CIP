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
use App\Traits\SendEmailTrait;

class CompileScssFiles extends Job
{
    use RestExceptionHandlerTrait, SendEmailTrait;

    /**
     * @var string
     */
    private $tenantName;

    /**
     * @var string
     */
    private $emailMessage;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $tenantName)
    {
        $this->tenantName = $tenantName;
        $this->emailMessage = 'Job passed successufully';
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
        
        if (!file_exists(base_path()."/node_modules/bootstrap/scss/bootstrap.scss")
            || !file_exists(base_path()."/node_modules/bootstrap-vue/src/index.js")) {
            // Send error like bootstrap.scss not found while compile files
            throw new FileNotFoundException(
                trans('messages.custom_error_message.ERROR_BOOSTRAP_SCSS_NOT_FOUND'),
                config('constants.error_codes.ERROR_BOOSTRAP_SCSS_NOT_FOUND')
            );
        }

        try {
            $importScss =
            '@import "_assets";
            $assetUrl: "'.$assetUrl.'";
            @import "_variables";
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
                try {
                    Storage::disk('s3')->put(
                        $this->tenantName.'/assets/css/style.css',
                        Storage::disk('local')->get($this->tenantName.'\assets\css\style.css')
                    );
                } catch (S3Exception $e) {
                    $this->emailMessage = trans(
                        'messages.custom_error_message.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3'
                    );

                    return $this->s3Exception(
                        config('constants.error_codes.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3'),
                        trans('messages.custom_error_message.ERROR_FAILD_TO_UPLOAD_COMPILE_FILE_ON_S3')
                    );
                }
            } else {
                $this->emailMessage = trans(
                    'messages.custom_error_message.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL'
                );
                
                throw new FileDownloadException(
                    trans('messages.custom_error_message.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL'),
                    config('constants.error_codes.ERROR_WHILE_STORE_COMPILED_CSS_FILE_TO_LOCAL')
                );
            }
        } catch (ParserException $e) {
            $this->emailMessage = trans('messages.custom_error_message.ERROR_WHILE_COMPILING_SCSS_FILES');

            throw new ParserException(
                trans('messages.custom_error_message.ERROR_WHILE_COMPILING_SCSS_FILES'),
                config('constants.error_codes.ERROR_WHILE_COMPILING_SCSS_FILES')
            );
        } catch (\Exception $e) {
            $this->emailMessage = trans('messages.custom_error_message.ERROR_WHILE_COMPILING_SCSS_FILES');
            throw $e;
        }
        // Send success mail to super admin
        $this->emailMessage = 'Job executed successfully';
        $this->sendEmailNotification();
    }

    /**
     * Send email notification to admin
     * @param bool $isFail
     * @return void
     */
    public function sendEmailNotification(bool $isFail = false)
    {
        $status = ($isFail) ? 'Failed' : 'Passed';

        $message = "<p> Tenant : " .$this->tenantName. "<br>";
        $message .= "Background Job Name : Compile SCSS Files <br>";
        $message .= "Background Job Status : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenantName
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
        $params['subject'] = ($isFail) ? 'Error: Compile SCSS Files Job For '.$this->tenantName. ' Tenant' :
        'Success: Compile SCSS Files Job For '.$this->tenantName. ' Tenant'; //optional
        $params['data'] = $data;

        $this->sendEmail($params);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        $this->sendEmailNotification(true);
    }
}
