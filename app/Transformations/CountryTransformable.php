<?php
namespace App\Transformations;

trait CountryTransformable
{
    /**
     * Country transformation.
     *
     * @param array $countryList
     * @param int $languageId
     * @param int $defaultLanguageId
     * @return Array
     */
    public function countryTransform(array $countryList, int $languageId, int $defaultLanguageId): Array
    {
        $countryData = array();
        foreach ($countryList as $key => $value) {
            $index = array_search($languageId, array_column($value['languages'], 'language_id'));
            if ($index !== false) {
                $countryData[$value['languages'][$index]['country_id']] = $value['languages'][$index]['name'];
            } else {
                $translationIndex = array_search(
                    $defaultTenantlanguage,
                    array_column($value['languages'], 'language_id')
                );
                if ($translationIndex) {
                    $countryData[$value['languages'][$translationIndex]['country_id']]
                    = $value['languages'][$translationIndex]['name'];
                } else {
                    $countryData[$value['languages'][$index]['country_id']] = $value['languages'][0]['name'];
                }
            }
        }
        return $countryData;
    }
}
