<?php

namespace App\Repositories\Organization;

use App\Repositories\Organization\OrganizationInterface;
use App\Models\Organization;
use Illuminate\Http\Request;
use \Illuminate\Pagination\LengthAwarePaginator;
use Ramsey\Uuid\Uuid;
use App\Models\Mission;

class OrganizationRepository implements OrganizationInterface
{
    /**
     * @var App\Models\Organization
     */
    private $organization;

    /**
     * @var App\Models\Mission
     */
    private $mission;

    /**
     * Create a new organization repository instance.
     *
     * @param  App\Models\Organization $organization
     * @return void
     */
    public function __construct(
        Organization $organization,
        Mission $mission
    ) {
        $this->organization = $organization;
        $this->mission = $mission;
    }

    /**
     * Store organization details.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Organization
     */
    public function store(Request $request): Organization
    {
        // Store organization details
        $request->request->add(['organization_id'=> Uuid::uuid4()->toString()]);
        $organization = $this->organization->create($request->all());

        return $organization;
    }

    /**
     * Update organization.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $organizationId
     * @return App\Models\Organization
     */
    public function update(Request $request, $organizationId)
    {
        $organizationDetails = $this->organization->findOrfail($organizationId);
        $organizationDetails->update($request->toArray());
        return $organizationDetails;
    }

    /**
     * Remove organization.
     *
     * @param string $organizationId
     * @return bool
     */
    public function delete($organizationId)
    {
        $organization = $this->organization->findOrfail($organizationId);
        return $organization->delete();
    }

    /**
     * Get organization details.
     *
     * @param string $organizationId
     * @return App\Models\Organization
     */
    public function getOrganizationDetails($organizationId)
    {
        return $this->organization->findOrfail($organizationId);
    }

    /**
     * Display organization lists.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOrganizationList(Request $request): LengthAwarePaginator
    {
        $organizationData = $this->organization;

        // Search filters
        if ($request->has('search')) {
            $organizationData = $organizationData->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $organizationData = $organizationData->orderBy('created_at', $orderDirection);
        }

        return $organizationData->paginate($request->perPage);
    }

    /**
     * find organization.
     *
     * @param string $organizationId
     * @return App\Models\Organization
     */
    public function find($organizationId)
    {
        return $this->organization->find($organizationId);
    }

    /**
     * Check organization linked to mission or not
     *
     * @param string $organizationId
     * @return integer
     */
    public function isOrganizationLinkedtoMission($organizationId)
    {
        return $this->mission->where('organisation_id', $organizationId)->count();
    }
}
