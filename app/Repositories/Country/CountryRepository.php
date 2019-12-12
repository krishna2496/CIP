<?php
namespace App\Repositories\Country;

use Illuminate\Http\Request;
use App\Repositories\Country\CountryInterface;
use App\Models\Country;
use App\Models\CountryLanguage;
use Illuminate\Support\Collection;
use App\Helpers\LanguageHelper;

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
     * @param int $defaultLanguageId
     * @return array
     */
    public function getCountry(int $countryId, int $languageId, int $defaultLanguageId) : array
    {
       
        $country = $this->country->with('languages')->where("country_id", $countryId)->first();
        $translation = $country->languages->toArray();

        $translationkey = '';
        if (array_search($languageId, array_column($translation, 'language_id')) !== false) {
            $translationkey = array_search($languageId, array_column($translation, 'language_id'));
        } elseif (array_search($defaultLanguageId, array_column($translation, 'language_id')) !== false) {
            $translationkey = array_search($defaultLanguageId, array_column($translation, 'language_id'));
        }
    
        if ($translationkey !== '') {
            $countryData = array('country_id' => $country->country_id,
            'country_code' => $country->ISO,
            'name' => $translation[$translationkey]['name'],
            );
        } else {
            $countryData = array('country_id' => $country->country_id,
            'country_code' => $country->ISO,
            'name' => $country->name,
            );
        }
      
        return $countryData;
    }

    /**
     * Store a newly created resource in storage
     *
     * @param string $iso
     * @return App\Models\Country
     */
    public function store(string $iso): Country
    {
        return $this->country->create(['ISO' => $iso]);
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
            $countryDetail['iso'] = $request['iso'];
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
}
