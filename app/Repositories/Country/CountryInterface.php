<?php

namespace App\Repositories\Country;

use Illuminate\Http\Request;

interface CountryInterface
{
    public function countryList(Request $request);
}
