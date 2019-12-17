<?php
namespace App\Transformations;

trait CountryTransformable
{
    /**
     * Country transformation.
     *
     * @param array $countryList
     * @param int $languageId
     * @return Array
     */
    public function countryTransform(array $countryList, int $languageId): Array
    {
        $countryData = array();
        foreach ($countryList as $key => $value) {
            $index = array_search($languageId, array_column($value['languages'], 'language_id'));
            if ($index !== false) {
                $countryData[$value['languages'][$index]['country_id']] = $value['languages'][$index]['name'];
            }
        }
        return $countryData;
    }
}
