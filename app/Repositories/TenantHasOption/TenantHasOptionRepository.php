<?php
namespace App\Repositories\TenantHasOption;

use App\Repositories\TenantHasOption\TenantHasOptionInterface;
use Illuminate\Http\Request;
use App\Models\TenantHasOption;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TenantHasOptionRepository implements TenantHasOptionInterface
{
    /**
     * @var App\Models\TenantHasOption
     */
    public $tenantHasOption;

    /**
     * Create a new Tenant repository instance.
     *
     * @param  App\Models\TenantHasOption $tenantHasOption
     * @return void
     */
    public function __construct(TenantHasOption $tenantHasOption)
    {
        $this->tenantHasOption = $tenantHasOption;
    }
}
