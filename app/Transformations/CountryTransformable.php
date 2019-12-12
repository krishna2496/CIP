<?php
namespace App\Transformations;

trait CountryTransformable
{
    /**
     * Country transformation.
     *
     * @param array $countryList
     * @param int $languageId 
     * @param int $defaultTenantlanguage 
     * @return Array
     */
    public function countryTransform(array $countryList, int $languageId, int $defaultTenantlanguage): Array
    {
        foreach ($countryList as $key => $value) {
            $index = array_search($languageId, array_column($value['translations'], 'language_id'));
            if ($index !== false) {
                $countryData[$value['translations'][$index]['country_id']] = $value['translations'][$index]['name'];
            } else {
                $translationIndex = array_search($defaultTenantlanguage, array_column($value['translations'], 'language_id'));
                if ($translationIndex) {
                    $countryData[$value['translations'][$translationIndex]['country_id']] = $value['translations'][$translationIndex]['name'];                
                } else {
                    $countryData[$value['translations'][$index]['country_id']] = $value['translations'][0]['name'];
                }
            }
        }
        return $countryData;
    }
}
