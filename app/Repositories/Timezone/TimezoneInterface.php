<?php
namespace App\Repositories\Timezone;

use Illuminate\Http\Request;
use App\Models\Timezone;

interface TimezoneInterface
{
    /**
     * Display timezone
     *
     * @param \Illuminate\Http\Request $request
     * @param int $timezone_id
     * @return App\Models\Timezone
     */
    public function timezoneList(Request $request, int $timezone_id = null) :Timezone;
}
