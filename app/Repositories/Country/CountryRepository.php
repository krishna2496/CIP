<?php
namespace App\Repositories\Country;

use Illuminate\Http\Request;
use App\Repositories\Country\CountryInterface;
use App\Models\Country;
use App\Models\CountryLanguage;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryRepository implements CountryInterface
{
    /**
     * @var App\Models\Country
     */
    public $country;

    /**
     * @var App\Models\CountryLanguage
     */
    public $countryLanguage;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;
    
    /**
     * Create a new repository instance.
     *
     * @param App\Models\Country $country
     * @param App\Models\CountryLanguage $countryLanguage
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(Country $country, CountryLanguage $countryLanguage, LanguageHelper $languageHelper)
    {
        $this->country = $country;
        $this->countryLanguage = $countryLanguage;
        $this->languageHelper = $languageHelper;
    }
    
    /**
    * Get a listing of resource.
    *
    * @return Illuminate\Support\Collection
    */
    public function countryList(): Collection
    {
        return $this->country->with('languages')->get();
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
     * @param int $languageId
     * @return array
     */
    public function getCountry(int $countryId, int $languageId) : array
    {
        $country = $this->country->with('languages')->where("country_id", $countryId)->first();
        $translation = $country->languages->toArray();

        $countryData = array('country_id' => $country->country_id,
            'country_code' => $country->ISO,
            'name' =>  $translation[0]['name'] ?? '',
            );

        $translationkey = '';
        if (array_search($languageId, array_column($translation, 'language_id')) !== false) {
            $translationkey = array_search($languageId, array_column($translation, 'language_id'));
        }
    
        if ($translationkey !== '') {
            $countryData = array('country_id' => $country->country_id,
            'country_code' => $country->ISO,
            'name' => $translation[$translationkey]['name'],
            );
        }
        return $countryData;
    }

    /**
     * Store a newly created resource in storage
     *
     * @param array $countryData
     * @return App\Models\Country
     */
    public function store(array $countryData): Country
    {
        $insertedCountry = $this->country->create(['ISO' => $countryData['iso']]);
        $languages = $this->languageHelper->getLanguages();

        foreach ($countryData['translations'] as $key => $country) {
            $data = [];
            $languageId = $languages->where('code', $country['lang'])->first()->language_id;
            
            $data['country_id'] = $insertedCountry['country_id'];
            $data['language_id'] = $languageId;
            $data['name'] = $country['name'];
            
            $this->countryLanguage->create($data);
        }
        return $insertedCountry;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->country->deleteCountry($id);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param \Illuminate\Http\Request $request
    * @param int $id
    * @return App\Models\Country
    */
    public function update(Request $request, int $id): Country
    {
        // Set data for update record
        $countryDetail = array();
        if (isset($request['iso'])) {
            $countryDetail['ISO'] = $request['iso'];
        }

        // Update country
        $countryData = $this->country->findOrFail($id);
        $countryData->update($countryDetail);
        $languages = $this->languageHelper->getLanguages();
                 
        if (isset($request['translations'])) {
            foreach ($request['translations'] as $value) {
                $language = $languages->where('code', $value['lang'])->first();
                $countryLanguageData = [
                    'country_id' => $id,
                    'name' => $value['name'],
                    'language_id' => $language->language_id
                ];

                $this->countryLanguage->createOrUpdateCountryLanguage(['country_id' => $id,
                 'language_id' => $language->language_id], $countryLanguageData);
                unset($countryLanguageData);
            }
        }
        return $countryData;
    }

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\Country
     */
    public function find(int $id): Country
    {
        return $this->country->findOrFail($id);
    }

    /**
    * Get a listing of resource.
    *
    * @param Illuminate\Http\Request $request
    * @return Illuminate\Pagination\LengthAwarePaginator
    */
    public function getCountryList(Request $request): LengthAwarePaginator
    {
        $countryQuery = $this->country->with(['languages']);

        if ($request->has('search') && $request->input('search') != '') {
            $countryQuery->wherehas('languages', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%');
            });
        }

        return $countryQuery->paginate($request->perPage);
    }

    /**
     * It will check is country belongs to any mission or not
     *
     * @param int $id
     * @return bool
     */
    public function hasMission(int $id): bool
    {
        return $this->country->whereHas('mission')->whereCountryId($id)->count() ? true : false;
    }

    /**
     * It will check is country belongs to any user or not
     *
     * @param int $id
     * @return bool
     */
    public function hasUser(int $id): bool
    {
        return $this->country->whereHas('user')->whereCountryId($id)->count() ? true : false;
    }
}
