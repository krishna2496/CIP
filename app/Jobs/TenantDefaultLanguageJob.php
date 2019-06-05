<?php

namespace App\Jobs;
use App\Helpers\Helpers;
use App\Tenant;
use App\Language;
use DB;
class TenantDefaultLanguageJob extends Job
{

    protected $tenant;
    protected $language;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant, Language $language)
    {        
        $this->tenant = $tenant;
        $this->language = $language;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        try{
            $data = array(
                [
                    'option_name' => 'supported_languages',
                    'option_value' => json_encode(
                        [
                            'id' => $this->language->language_id, 
                            'code' => $this->language->code
                        ]
                    )
                ],
                [
                    'option_name' => 'default_language',
                    'option_value' => json_encode(
                        [
                            'id' => $this->language->language_id, 
                            'code' => $this->language->code
                        ]
                    )
                ]
            );
            DB::table('tenant_option')->insert($data);
        } catch(\Exception $e){
            // Send tenant language insert time error.
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_10010'), 
                                        config('errors.custom_error_message.10010'));
        }
    }
}
