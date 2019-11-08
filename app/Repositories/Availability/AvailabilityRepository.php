<?php

namespace App\Repositories\Availability;

use App\Models\Availability;
use App\Repositories\Availability\AvailabilityInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AvailabilityRepository implements AvailabilityInterface
{
    /**
     *
     * @var App\Models\Availability
     */
    private $availability;

    /**
     * Create a new availability repository instance.
     *
     * @param App\Models\Availability $availability
     * @return void
     */
    public function __construct(Availability $availability) {
        $this->availability = $availability;
    }

    /**
     * Fetch availability lists with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAvailabilityList(Request $request): LengthAwarePaginator {
        return $this->availability->paginate($request->perPage);
    }

    /**
     * Store a newly created availability details.
     *
     * @param array $availabilityData
     * @return App\Models\Availability
     */
    public function store(array $availabilityData): Availability
    {
        return $this->availability->create($availabilityData);
    }

    /**
     * Update availability details.
     *
     * @param  array  $availabilityData
     * @param  int  $availabilityId
     * @return App\Models\Availability
     */
    public function update(array $availabilityData, int $availabilityId): Availability
    {
        $availabilityDetails = $this->availability->findOrFail($availabilityId);
        $availabilityDetails->update($availabilityData);
        return $availabilityDetails;
    }

    /**
     * Remove availability details.
     *
     * @param int $availabilityId
     * @return bool
     */
    public function delete(int $availabilityId): bool
    {
        return $this->availability->deleteAvailability($availabilityId);
    }
    
    /**
     * Find availability details.
     *
     * @param  int  $id
     * @return App\Models\Availability
     */
    public function find(int $id): Availability
    {
        return $this->availability->findOrFail($id);
    }
}
