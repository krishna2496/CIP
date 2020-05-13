<?php

namespace App\Console\Commands;

use App\Jobs\RecompileCustomStylesJob;
use App\Repositories\TenantOption\TenantOptionRepository;

class RecompileCustomStyles extends MultiTenantAware
{

    protected $signature = 'styles:custom:recompile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This email notification to users";

    /**
     * @var TenantOptionRepository
     */
    private $tenantOptionRepository;

    public function __construct(TenantOptionRepository $tenantOptionRepository)
    {
        parent::__construct();
        $this->tenantOptionRepository = $tenantOptionRepository;
    }

    /**
     * @inheritDoc
     */
    protected function handleTenant($tenantId, $tenantName): void
    {
        try {
            $tenantOptions = $this->tenantOptionRepository->getOptionWithCondition(['option_name' => 'custom_css']);
            $isCustomCssDisabled = $tenantOptions === null;

            // Skip this tenant if custom css disabled
            if ($isCustomCssDisabled) {
                return;
            }
        } catch (\Exception $e) {
            /*
             * If we encounter an error while trying to retrieve the option, it means
             * we cannot be sure if custom CSS are enabled or not, it might be safer
             * to therefore skip the recompilation for this one
             */
            return;
        }

        $this->info("\nRecompiling SCSS with latest styles for tenant ${tenantId} [${tenantName}]\n");
        dispatch(new RecompileCustomStylesJob($tenantName));
    }
}
