<?php
namespace App\Repositories\Organization;

use App\Repositories\Organization\OrganizationInterface;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationRepository implements OrganizationInterface
{
    /**
     * @var App\Models\Organization
     */
    private $organization;

    /**
     * Create a new organization repository instance.
     *
     * @param  App\Models\Organization $organization
     * @return void
     */
    public function __construct(
        Organization $organization
    ) {
        $this->organization = $organization;
    }  
}
