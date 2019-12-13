<?php
namespace App\Transformations;

trait CityTransformable
{
    /**
     * City transformation.
     *
     * @param array $cityList
     * @param int $languageId 
     * @param int $defaultTenantlanguage 
     * @return Array
     */
    public function cityTransform(array $cityList,int $languageId, int $defaultTenantlanguage): Array
    {
        foreach ($cityList as $key => $value) {
            $index = array_search($languageId, array_column($value['translations'], 'language_id'));
            if ($index !== false) {
                $cityData[$value['translations'][$index]['city_id']] = $value['translations'][$index]['name'];
            } else {
                $translationIndex = array_search($defaultTenantlanguage, array_column($value['translations'], 'language_id'));
                if ($translationIndex) {
                    $cityData[$value['translations'][$translationIndex]['city_id']] = $value['translations'][$translationIndex]['name'];                
                } else {
                    $cityData[$value['translations'][$index]['city_id']] = $value['translations'][0]['name'];
                }
            }
        }
        return $cityData;
    }
}
