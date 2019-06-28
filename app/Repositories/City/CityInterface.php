<?php

namespace App\Repositories\City;

use Illuminate\Http\Request;

interface CityInterface
{
    // public function save(array $data);
    public function cityList(Request $request);
}
