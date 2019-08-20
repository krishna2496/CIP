<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;
use App\Helpers\DatabaseHelper;
use DB;
use App\Traits\SendEmailTrait;

class CreateFolderInS3BucketJob extends Job
{
    use SendEmailTrait;

    /**
     * @var App\Models\Tenant
     */
    private $tenant;
    
    /**
     * @var App\Helpers\DatabaseHelper
     */
    protected $databaseHelper;

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
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->databaseHelper = new DatabaseHelper;
        $this->emailMessage = 'Job passed successufully';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Create folder on S3 using tenant's FQDN
            Storage::disk('s3')->makeDirectory($this->tenant->name);

            // Copy default_theme folder which is already present on S3
            if (Storage::disk('s3')->exists(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'))) {
                $files = Storage::disk('s3')->allFiles(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'));

                // Fetched files copy to created s3 folder
                foreach ($files as $key => $file) {
                    $sourcePath = str_replace(env('AWS_S3_DEFAULT_THEME_FOLDER_NAME'), '', $file);

                    // Delete if folder already exists
                    if (Storage::disk('s3')->exists($this->tenant->name.'/'.$sourcePath)) {
                        Storage::disk('s3')->delete($this->tenant->name.'/'.$sourcePath);
                    }
                
                    // Copy and paste file into tenant's folders
                    Storage::disk('s3')->copy($file, $this->tenant->name.'/'.$sourcePath);
                    
                    if (!strpos($file, env('AWS_S3_IMAGES_FOLDER_NAME', "/images"))) {
                        Storage::disk('local')->put(
                            $this->tenant->name.'/'.$sourcePath,
                            Storage::disk('s3')->get($file)
                        );
                    }
                    
                    // Insert default logo image in database
                    if (strpos(
                        $file,
                        env('AWS_S3_IMAGES_FOLDER_NAME').'/'.config('constants.AWS_S3_LOGO_IMAGE_NAME')
                    )) {
                        $logoPathInS3 = 'https://s3.'.env('AWS_REGION').'.amazonaws.com/'.
                        env('AWS_S3_BUCKET_NAME').'/'.$this->tenant->name.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').
                        '/'.env('AWS_S3_IMAGES_FOLDER_NAME').'/'.config('constants.AWS_S3_LOGO_IMAGE_NAME');

                        // Connect with tenant database
                        $tenantOptionData['option_name'] = "custom_logo";
                        $tenantOptionData['option_value'] = $logoPathInS3;

                        // Create connection with tenant database
                        $this->databaseHelper->connectWithTenantDatabase($this->tenant->tenant_id);
                        DB::table('tenant_option')->insert($tenantOptionData);

                        // Disconnect tenant database and reconnect with default database
                        DB::disconnect('tenant');
                        DB::reconnect('mysql');
                        DB::setDefaultConnection('mysql');
                    }

                    if (basename($file)==env('S3_CUSTOME_CSS_NAME')) {
                        $pathInS3 = 'https://s3.'.env('AWS_REGION').'.amazonaws.com/'.
                        env('AWS_S3_BUCKET_NAME').'/'.$this->tenant->name.''.$sourcePath;
                        
                        // Connect with tenant database
                        $tenantOptionData['option_name'] = "custom_css";
                        $tenantOptionData['option_value'] = $pathInS3;

                        // Create connection with tenant database
                        $this->databaseHelper->connectWithTenantDatabase($this->tenant->tenant_id);
                        DB::table('tenant_option')->insert($tenantOptionData);

                        // Disconnect tenant database and reconnect with default database
                        DB::disconnect('tenant');
                        DB::reconnect('mysql');
                        DB::setDefaultConnection('mysql');
                    }
                }
                // Send success mail to super admin
                $this->sendEmailNotification();
            }
        } catch (\Exception $e) {
            throw $e;
            $this->emailMessage = "Error while creating folder on S3 bucket";
        }
    }

    /**
     * Send email notification to admin
     * @param bool $isFail
     * @return void
     */
    public function sendEmailNotification(bool $isFail = false)
    {
        $status = ($isFail) ? 'Failed' : 'Passed';

        $message = "<p> Tenant : " .$this->tenant->name. "<br>";
        $message .= "Background Job Name : Create Folder On S3 Bucket <br>";
        $message .= "Background Job Status : ".$status." <br>";

        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
        $params['subject'] = ($isFail) ? 'Error: Create Folder On S3 Bucket Job For '.$this->tenant->name. ' Tenant' :
        'Success: Create Folder On S3 Bucket Job For '.$this->tenant->name. ' Tenant'; //optional
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
