<?php

namespace App\Repositories\Country;

use App\Repositories\Country\CountryInterface;
use Illuminate\Http\{Request};
use App\Models\Country;

class CountryRepository implements CountryInterface
{
	/**
	 * @var App\Models\Country 
	 */
    public $country;

    function __construct(Country $country) {
		$this->country = $country;
    }		
	
	public function countryList(Request $request) 
	{
			return $this->country->pluck('name','country_id');	
	}

}