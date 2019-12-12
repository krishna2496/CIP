<?php
namespace App\Repositories\City;

use App\Repositories\City\CityInterface;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Collection;

class CityRepository implements CityInterface
{
    /**
     * @var App\Models\City
     */
    public $city;

    /**
     * @var App\Models\Country
     */
    public $country;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\City $city
     * @param App\Models\Country $country
     * @return void
     */
    public function __construct(City $city, Country $country)
    {
        $this->city = $city;
        $this->country = $country;
    }
    
    /**
    * Get listing of all city.
    *
    * @param int $countryId
    * @return Illuminate\Support\Collection
    */
    public function cityList(int $countryId): Collection
    {
        $this->country->findOrFail($countryId);
        return $this->city->with('translations')->where('country_id', $countryId)->get();
    }

    /**
     * Get city data from cityId
     *
     * @param string $cityId
     * @param int $languageId
     * @param int $defaultLanguageId
     * @return array
     */
    public function getCity(string $cityId, int $languageId, int $defaultLanguageId) : array
    {
        $city = $this->city->with('translations')->whereIn("city_id", explode(",", $cityId))->get()->toArray();
        
        $cityData = [];
        if (!empty($city)) {
            foreach ($city as $key => $value) {
                $translation = $value['translations'];
                $translationkey = '';
                if (array_search($languageId, array_column($translation, 'language_id')) !== false) {
                    $translationkey = array_search($languageId, array_column($translation, 'language_id'));
                } else if(array_search($defaultLanguageId, array_column($translation, 'language_id')) !== false) {
                    $translationkey = array_search($defaultLanguageId, array_column($translation, 'language_id'));
                }
           
                if ($translationkey !== '') {
                   
                    $cityData[$value['city_id']] = $translation[$translationkey]['name'];
                }
            }
        }
        return $cityData;
    }
    
    /**
     * Store city data
     *
     * @param string $countryId
     * @return City
     */
    public function store(string $countryId): City
    {
        return $this->city->create(['country_id' => $countryId]);
    }

    /**
     * Get listing of all city.
     *
     * @return Illuminate\Support\Collection
     */
    public function cityLists(): Collection
    {
        return $this->city->with(['translations'])->get();
    }

    /**
     * City transformation.
     *
     * @param array $cityList
     * @param int $languageId 
     * @return Array
     */
    public function cityTransform(array $cityList,int $languageId): Array
    {
        foreach ($cityList as $key => $value) {
            $index = array_search($languageId, array_column($value['translations'], 'language_id'));
            if ($index) {
                $cityData[$value['translations'][$index]['city_id']] = $value['translations'][$index]['name'];                
            } else {
                $cityData[$value['translations'][$index]['city_id']] = $value['translations'][0]['name'];
            }
        }
        return $cityData;
    }
}
