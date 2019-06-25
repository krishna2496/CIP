<?php
namespace App\Repositories\City;

use App\Repositories\City\CityInterface;
use Illuminate\Http\Request;
use App\Models\City;

class CityRepository implements CityInterface
{
    /**
     * @var App\Models\City
     */
    public $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }
    
    public function cityList(Request $request)
    {
        return $this->city->pluck('name', 'city_id');
    }
}
