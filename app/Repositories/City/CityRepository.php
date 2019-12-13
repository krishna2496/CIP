<?php
namespace App\Repositories\City;

use Illuminate\Http\Request;
use App\Repositories\City\CityInterface;
use App\Models\City;
use App\Models\CityLanguage;
use App\Models\Country;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Pagination\LengthAwarePaginator;

class CityRepository implements CityInterface
{
    /**
     * @var App\Models\City
     */
    public $city;

    /**
     * @var App\Models\CityLanguage
     */
    public $cityLanguage;

    /**
     * @var App\Models\Country
     */
    public $country;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new repository instance.
     *
     * @param App\Models\City $city
     * @param App\Models\Country $country
     * @param App\Models\CityLanguage $cityLanguage
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        City $city,
        Country $country,
        CityLanguage $cityLanguage,
        LanguageHelper $languageHelper
    ) {
        $this->city = $city;
        $this->country = $country;
        $this->cityLanguage = $cityLanguage;
        $this->languageHelper = $languageHelper;
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
        return $this->city->with('languages')->where('country_id', $countryId)->get();
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
        $city = $this->city->with('languages')->whereIn("city_id", explode(",", $cityId))->get()->toArray();
       
        $cityData = [];
        if (!empty($city)) {
            foreach ($city as $key => $value) {
                $translation = $value['languages'];
                $translationkey = '';
                if (array_search($languageId, array_column($translation, 'language_id')) !== false) {
                    $translationkey = array_search($languageId, array_column($translation, 'language_id'));
                } elseif (array_search($defaultLanguageId, array_column($translation, 'language_id')) !== false) {
                    $translationkey = array_search($defaultLanguageId, array_column($translation, 'language_id'));
                }
           
                if ($translationkey !== '') {
                    $cityData[$value['city_id']] = $translation[$translationkey]['name'];
                } else {
                    $cityData[$value['city_id']] =  $translation[0]['name'] ?? '';
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
     * Store city language data
     *
     * @param array $cityData
     * @return void
     */
    public function storeCityLanguage(array $cityData)
    {
        $languages = $this->languageHelper->getLanguages();
        
        foreach ($cityData['translations'] as $key => $city) {
            $data = [];
            $languageId = $languages->where('code', $city['lang'])->first()->language_id;
            
            $data['city_id'] = $cityData['city_id'];
            $data['language_id'] = $languageId;
            $data['name'] = $city['name'];
            
            $this->cityLanguage->create($data);
        }
    }

    /**
     * Get listing of all city.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function cityLists(Request $request): LengthAwarePaginator
    {
        $cityQuery = $this->city->with(['languages']);

        if ($request->has('search') && $request->input('search') != '') {
            $cityQuery->wherehas('languages', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%');
            });
        }

        return $cityQuery->paginate($request->perPage);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->city->deleteCity($id);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  int $id
    * @return App\Models\City
    */
    public function update(Request $request, int $id): City
    {
        // Set data for update record
        $cityDetail = array();
        if (isset($request['country_id'])) {
            $cityDetail['country_id'] = $request['country_id'];
        }
        
        // Update city
        $cityData = $this->city->findOrFail($id);
        $cityData->update($cityDetail);
        
        $languages = $this->languageHelper->getLanguages();
                 
        if (isset($request['translations'])) {
            foreach ($request['translations'] as $value) {
                $language = $languages->where('code', $value['lang'])->first();
                $cityLanguageData = [
                    'city_id' => $id,
                    'name' => $value['name'],
                    'language_id' => $language->language_id
                ];

                $this->cityLanguage->createOrUpdateCityLanguage(['city_id' => $id,
                 'language_id' => $language->language_id], $cityLanguageData);
                unset($cityLanguageData);
            }
        }
        return $cityData;
    }

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\City
     */
    public function find(int $id): City
    {
        return $this->city->findOrFail($id);
    }
}
