<?php

namespace App\Jobs;
use App\Helpers\Helpers;
use App\Tenant;
use App\Language;
use DB;
class TenantDefaultLanguageJob extends Job
{

    protected $tenant;

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
        try{
            // Add default English and French language for tenant which will be created.
            $defaultData = array(
                ['language_id' => 1, 'default' => '1'],
                ['language_id' => 2, 'default' => '0']
            );
            foreach ($defaultData as $key => $data) {
                $this->tenant->languages()->create($data);
            }
        } catch(\Exception $e){            
            // Send tenant language insert time error.
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_10010'), 
                                        config('errors.custom_error_message.10010'));
        }
    }
}
