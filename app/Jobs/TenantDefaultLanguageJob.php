<?php
namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

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
        // Job will try to attempt only one time. If need to re-attempt then it will delete job from table
        if ($this->attempts() < 2) {
            // do job things
            try {
                // Add default English and French language for tenant - Testing purpose
                $defaultData = array(
                    ['language_id' => 1, 'default' => '1'],
                    ['language_id' => 2, 'default' => '0']
                );
                foreach ($defaultData as $key => $data) {
                    $this->tenant->tenantLanguages()->create($data);
                }
            } catch (\Exception $e) {
                Log::info($this->tenant->name." deleted. Because of default language job have some issue");

                // Delete tenant from database
                $this->tenant->delete();
            }
        } else {
            Log::info("Tenant" .$this->tenant->name."'s : 
            default language job have some issue. So, job is deleted from database");

            // Delete job from database
            $this->delete();
        }
    }
}
