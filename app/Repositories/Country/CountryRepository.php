<?php
namespace App\Repositories\Country;

use App\Repositories\Country\CountryInterface;
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
    * @return Illuminate\Support\Collection
    */
    public function countryList(): Collection
    {
        return $this->country->with('translations')->get();
    }

    /**
     * Get country id from country code
     *
     * @param string $countryCode
     * @return int
     */
    public function getCountryId(string $countryCode) : int
    {
        return $this->country->where("ISO", $countryCode)->first()->country_id;
    }

    /**
     * Get country detail from country_id
     *
     * @param int  $countryId
     * @return array
     */
    public function getCountry(int $countryId) : array
    {
        $country = $this->country->where("country_id", $countryId)->first();
        $countryData = array('country_id' => $country->country_id,
                             'country_code' => $country->ISO,
                             'name' => $country->name,
                            );
        return $countryData;
    }


    public function store(string $iso): Country
    {
        return $this->country->create(['ISO' => $iso]);
    }

    /**
     * Country transformation.
     *
     * @param array $countryList
     * @param int $languageId 
     * @return Array
     */
    public function countryTransform(array $countryList, int $languageId): Array
    {
        foreach ($countryList as $key => $value) {
            $index = array_search($languageId, array_column($value['translations'], 'language_id'));
            if ($index) {
                $countryData[$value['translations'][$index]['country_id']] = $value['translations'][$index]['name'];                
            } else {
                $countryData[$value['translations'][$index]['country_id']] = $value['translations'][0]['name'];
            }
        }
        return $countryData;
    }
}
