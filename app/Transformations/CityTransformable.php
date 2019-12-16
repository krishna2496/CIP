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
    public function cityTransform(array $cityList, int $languageId, int $defaultTenantlanguage): Array
    {
        foreach ($cityList as $key => $value) {
            $index = array_search($languageId, array_column($value['languages'], 'language_id'));
            if ($index !== false) {
                $cityData[$value['languages'][$index]['city_id']] = $value['languages'][$index]['name'];
            } else {
                $translationIndex = array_search(
                    $defaultTenantlanguage,
                    array_column($value['languages'], 'language_id')
                );
                if ($translationIndex) {
                    $cityData[$value['languages'][$translationIndex]['city_id']]
                    = $value['languages'][$translationIndex]['name'];
                } else {
                    $cityData[$value['languages'][$index]['city_id']] = $value['languages'][0]['name'];
                }
            }
        }
        return $cityData;
    }
}
