<?php
namespace App\Repositories\City;

use App\Repositories\City\CityInterface;
use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Collection;

class CityRepository implements CityInterface
{
    /**
     * @var App\Models\City
     */
    public $city;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\City $city
     * @return void
     */
    public function __construct(City $city)
    {
        $this->city = $city;
    }
    
    /**
    * Get listing of all city.
    *
    * @param int $countryId
    * @return Illuminate\Support\Collection
    */
    public function cityList(int $countryId): Collection
    {
        $this->city->where('country_id', $countryId)->firstOrFail();
        return $this->city->where('country_id', $countryId)->pluck('name', 'city_id');
    }
}
