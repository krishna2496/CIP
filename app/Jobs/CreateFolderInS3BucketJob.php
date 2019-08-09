<?php
namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant;
use App\Helpers\DatabaseHelper;
use DB;

class CreateFolderInS3BucketJob extends Job
{
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
                }
            } catch (\Exception $e) {
                Log::info("Tenant" .$this->tenant->name."'s : Create folder on S3 bucket job have some error.
                So, S3 folder, database and tenant deleted.");

                // Delete directory from s3
                Storage::disk('s3')->deleteDirectory($this->tenant->name);

                // Delete tenant database
                DB::statement("DROP DATABASE IF EXISTS `ci_tenant_{$this->tenant->tenant_id}`");

                // Delete created tenant
                $this->tenant->delete();
            }
        } else {
            Log::info("Tenant" .$this->tenant->name."'s : Create folder on S3 bucket job have some error.
            So, job deleted from database");
            // Delete job from database
            $this->delete();
        }
    }
}
