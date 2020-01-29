<?php
namespace App\Repositories\Timezone;

use App\Repositories\Timezone\TimezoneInterface;
use App\Models\Timezone;
use Illuminate\Support\Collection;

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
     * @param int $timezone_id
     * @return App\Models\Timezone
     */
    public function timezoneList(int $timezone_id = null) :Timezone
    {
        return $this->timezone->where("timezone_id", $timezone_id)->first();
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
