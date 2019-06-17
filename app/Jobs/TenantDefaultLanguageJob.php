<?php
namespace App\Jobs;

use App\Helpers\ResponseHelper;
use App\Models\Tenant;
use App\Language;
use DB;


class TenantDefaultLanguageJob extends Job
{
    /**
     * @var App\Models\Tenant
     */
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
        // Add default English and French language for tenant - Testing purpose
        $defaultData = array(
            ['language_id' => 1, 'default' => '1'],
            ['language_id' => 2, 'default' => '0']
        );
        foreach ($defaultData as $key => $data) {
            $this->tenant->tenantLanguages()->create($data);
        }        
    }
}
