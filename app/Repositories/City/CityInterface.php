<?php
namespace App\Repositories\City;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CityInterface
{
    /**
    * Get a listing of resource.
    *
    * @return Illuminate\Support\Collection
    */
    public function cityList(): Collection;
}
