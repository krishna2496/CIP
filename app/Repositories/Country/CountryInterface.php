<?php
namespace App\Repositories\Country;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CountryInterface
{
    /**
    * Get a listing of resource.
    *
    * @return Illuminate\Support\Collection
    */
    public function countryList(): Collection;
}
