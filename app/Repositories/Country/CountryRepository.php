<?php
namespace App\Repositories\Country;

use App\Repositories\Country\CountryInterface;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Collection;

class CountryRepository implements CountryInterface
{
    /**
     * @var App\Models\Country
     */
    public $country;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\Country $country
     * @return void
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }
    
    /**
    * Get a listing of resource.
    *
    * @param \Illuminate\Http\Request $request
    * @return Illuminate\Support\Collection
    */
    public function countryList(Request $request): Collection
    {
        return $this->country->pluck('name', 'country_id');
    }
}
