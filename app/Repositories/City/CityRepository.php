<?php

namespace App\Repositories\City;

use App\Repositories\City\CityInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\Models\City;

class CityRepository implements CityInterface
{
    public $city;
	
	private $response;

    function __construct(City $city, Response $response) {
		$this->city = $city;
		$this->response = $response;
    }		
	
	public function CityList(Request $request) 
	{
		try {
			$cityQuery = $this->city->pluck('name','city_id');
			return $cityQuery->toArray();
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
	}

    public function find(int $id) 
	{
		return $this->country->findCity($id);
	}

}