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
     * @param int $languageId
     * @param int $defaultLanguageId
     * @return array
     */
    public function getCountry(int $countryId, int $languageId, int $defaultLanguageId) : array
    {
       
        $country = $this->country->with('translations')->where("country_id", $countryId)->first();
        $translation = $country->translations->toArray();

        $translationkey = '';
        if (array_search($languageId, array_column($translation, 'language_id')) !== false) {
            $translationkey = array_search($languageId, array_column($translation, 'language_id'));
        } elseif(array_search($defaultLanguageId, array_column($translation, 'language_id')) !== false) {
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

    public function store(string $iso): Country
    {
        return $this->country->create(['ISO' => $iso]);
    }
}
