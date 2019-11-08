<?php
namespace App\Repositories\Availability;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface AvailabilityInterface
{
    /**
     * Fetch availability lists with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAvailabilityList(Request $request): LengthAwarePaginator;
}
