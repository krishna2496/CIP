<?php
namespace App\Repositories\Country;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface CountryInterface
{
    /**
    * Get a listing of resource.
    *
    * @return Illuminate\Support\Collection
    */
    public function countryList(): Collection;

    /**
     * Get country id from country code
     *
     * @param string $countryCode
     * @return int
     */
    public function getCountryId(string $countryCode) : int;

    /**
     * Get country detail from country_id
     *
     * @param int  $countryId
     * @param int $languageId
     * @param int $defaultLanguageId
     * @return array
     */
    public function getCountry(int $countryId, int $languageId, int $defaultLanguageId) : array;
}
