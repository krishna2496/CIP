<?php
namespace App\Jobs;

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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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

                if (basename($file)==env('S3_CUSTOME_CSS_NAME')) {
                    $pathInS3 = 'https://s3.'.env('AWS_REGION').'.amazonaws.com/'.
                    env('AWS_S3_BUCKET_NAME').'/'.$this->tenant->name.''.$sourcePath;
                    
                    // Connect with tenant database
                    $tenantOptionData['option_name'] = "custom_css";
                    $tenantOptionData['option_value'] = $pathInS3;

                    // Create connection with tenant database
                    DatabaseHelper::connectWithTenantDatabase($this->tenant->tenant_id);
                    DB::table('tenant_option')->insert($tenantOptionData);

                    // Disconnect tenant database and reconnect with default database
                    DB::disconnect('tenant');
                    DB::reconnect('mysql');
                    DB::setDefaultConnection('mysql');
                }
            }
        }
    }
}
