<?php
namespace App\Repositories\Timezone;

use App\Repositories\Timezone\TimezoneInterface;
use Illuminate\Http\Request;
use App\Models\Timezone;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use DB;

class TimezoneRepository implements TimezoneInterface
{
    /**
     * @var App\Models\Timezone
     */
    public $timezone;

    /**
     * Create a new Timezone repository instance.
     *
     * @param  App\Models\Timezone $timezone
     * @return void
     */
    public function __construct(Timezone $timezone)
    {
        $this->timezone = $timezone;
    }
    
    /**
     * Display timezone
     *
     * @param \Illuminate\Http\Request $request
     * @param int $timezone_id
     * @return App\Models\Timezone
     */
    public function timezoneList(Request $request, int $timezone_id = null) :Timezone
    {
        $timezone = $this->timezone->where("timezone_id", $timezone_id)->first();
        return $timezone;
    }

    /**
     * Get timezone list
     *
     * @return Illuminate\Support\Collection
     */
    public function getTimezoneList() :Collection
    {
        return $this->timezone->pluck('timezone', 'timezone_id');
    }
}
