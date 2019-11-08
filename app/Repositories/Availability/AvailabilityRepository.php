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
}
