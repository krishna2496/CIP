<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Tenant\TenantRepository;
use App\Jobs\TenantBackgroundJobsJob;
use App\Traits\RestExceptionHandlerTrait;

class TenantBackgroundProcessController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Tenant\TenantRepository
     */
    private $tenantRepository;

    /**
     * Create a new TenantBackgroundProcess controller instance.
     *
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @return void
     */
    public function __construct(TenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }
    

    public function runBackgroundProcess()
    {
        $tenants = $this->tenantRepository->getPendingTenantsForProcess();
        if ($tenants->count()) {
            foreach ($tenants as $tenant) {
                dispatch(new TenantBackgroundJobsJob($tenant));
            }
        }
    }
}
