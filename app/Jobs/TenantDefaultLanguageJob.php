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
     * Create a new job instance
     *
	 * @param App\Tenant $tenant
     * @return void
     */
    public function __construct(Tenant $tenant)
    {        
        $this->tenant = $tenant;
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {        
        try{
            // Add default English and French language for tenant - Testing purpose
            $defaultData = array(
                ['language_id' => 1, 'default' => '1'],
                ['language_id' => 2, 'default' => '0']
            );
            foreach ($defaultData as $key => $data) {
                $this->tenant->languages()->create($data);
            }
        } catch(\Exception $e){            
            // Any error occurs while operation
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_400'), 
										trans('api_error_messages.status_type.HTTP_STATUS_TYPE_400'), 
										trans('api_error_messages.custom_error_code.ERROR_10006'), 
										trans('api_error_messages.custom_error_message.10006'));
        }
    }
}
