<?php
namespace App\Transformations;

trait CityTransformable
{
    /**
     * City transformation.
     *
     * @param array $cityList
     * @param int $languageId
     * @return Array
     */
    public function cityTransform(array $cityList, int $languageId): Array
    {
        $cityData = array();
        foreach ($cityList as $key => $value) {
            $index = array_search($languageId, array_column($value['languages'], 'language_id'));
            if ($index !== false) {
                $cityData[$value['languages'][$index]['city_id']] = $value['languages'][$index]['name'];
            }
        }
        return $cityData;
    }
}
