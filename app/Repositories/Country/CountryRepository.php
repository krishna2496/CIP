<?php

namespace App\Repositories\Country;

use App\Repositories\Country\CountryInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\Models\Country;

class CountryRepository implements CountryInterface
{
    public $country;
	
	private $response;

    function __construct(Country $country, Response $response) {
		$this->country = $country;
		$this->response = $response;
    }		
	
	public function countryList(Request $request) 
	{
		try {
			$countryQuery = $this->country->pluck('name','country_id');
			return $countryQuery->toArray();
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
	}

    public function find(int $id) 
	{
		return $this->country->findCountry($id);
	}

}