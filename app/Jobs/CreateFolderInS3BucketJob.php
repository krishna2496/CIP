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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
        $this->databaseHelper = new DatabaseHelper;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Job will try to attempt only one time. If need to re-attempt then it will delete job from table
        if ($this->attempts() < 2) {
            // do job things
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
                    $message = "<p> Tenant : " .$this->tenant->name. "<br>";
                    $message .= "Background Job Name : Create Folder On S3 Bucket Job <br>";
                    $message .= "Background Job Status : Success <br>";

                    $this->sendEmailNotification($message);

                    Log::info($message);
                }
            } catch (\Exception $e) {
                $message = "<p> Tenant : " .$this->tenant->name. "<br>";
                $message .= "Background Job Name : Create Folder On S3 Bucket Job <br>";
                $message .= "Background Job Status : Failed <br>";
                $message .= "Message : S3 folder has been deleted. </p>";
    
                $this->sendEmailNotification($message, false);
    
                Log::info($message);

                // Delete directory from s3
                Storage::disk('s3')->deleteDirectory($this->tenant->name);

                // Delete tenant database
                //DB::statement("DROP DATABASE IF EXISTS `ci_tenant_{$this->tenant->tenant_id}`");

                // Delete created tenant
                // $this->tenant->delete();
            }
        } else {
            $message = "<p> Tenant : " .$this->tenant->name. "<br>";
            $message .= "Background Job Name : Create Folder On S3 Bucket Job <br>";
            $message .= "Background Job Status : Failed <br>";
            $message .= "Message : Job has been deleted from database. </p>";

            $this->sendEmailNotification($message, false);

            Log::info($message);

            // Delete job from database
            $this->delete();
        }
    }

    /**
     * Send email notification to admin
     * @param string $message
     * @param bool $isFail
     * @return void
     */
    public function sendEmailNotification(string $message, bool $isFail = false)
    {
        $data = array(
            'message'=> $message,
            'tenant_name' => $this->tenant->name
        );
        
        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
        $params['subject'] = ($isFail) ? 'Error in tenant creation : '. $this->tenant->name :
        'Success create folder on S3 bucket job : '.$this->tenant->name; //optional
        $params['data'] = $data;

        $this->sendEmail($params);
    }
}
