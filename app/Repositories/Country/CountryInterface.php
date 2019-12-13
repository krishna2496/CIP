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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
    * Update the specified resource in storage.
    *
    * @param \Illuminate\Http\Request $request
    * @param int $id
    * @return App\Models\Country
    */
    public function update(Request $request, int $id): Country;

    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\Country
     */
    public function find(int $id): Country;
}
