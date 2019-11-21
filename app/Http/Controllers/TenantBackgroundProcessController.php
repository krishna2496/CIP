<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Tenant\TenantRepository;
use App\Jobs\TenantBackgroundJobsJob;
use App\Traits\RestExceptionHandlerTrait;

//!  Tenantbackgroundprocess controller
/*!
This controller is responsible for handling tenant create background operation.
 */
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
    
    /**
     * Cron Job : Run tenant's background jobs for tenant create
     *
     * @param  int $tenantId
     * @return void
     */
    public function runBackgroundProcess($tenantId = null)
    {
        $tenants = $this->tenantRepository->getPendingTenantsForProcess($tenantId);
        if ($tenants->count()) {
            foreach ($tenants as $tenant) {
                dispatch(new TenantBackgroundJobsJob($tenant));
            }
        }
    }
}
