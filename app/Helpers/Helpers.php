<?php
namespace App\Helpers;

use Illuminate\Http\Request;

class Helpers
{

    /**
    * It will return
    * @param Illuminate\Http\Request $request
    * @return string
    */
    public static function getSubDomainFromRequest(Request $request)
    {
        try {
            return explode(".", parse_url($request->headers->all()['referer'][0])['host'])[0];
        } catch (\Exception $e) {
            if (env('APP_ENV')=='local') {
                return env('DEFAULT_TENANT');
            } else {
                return $e->getMessage();
            }
        }
    }

    /**
     * Get base URL from request object
     *
     * @param Illuminate\Http\Request $request
     * @return string
     */
    public static function getRefererFromRequest(Request $request)
    {
        try {
            if (isset($request->headers->all()['referer'])) {
                $parseUrl = parse_url($request->headers->all()['referer'][0]);
                return $parseUrl['scheme'].'://'.$parseUrl['host'].':'.$parseUrl['port'];
            } else {
                return env('APP_MAIL_BASE_URL');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Sorting of multidimensional array
     *
     * @param array $array
     * @param string $subfield
     * @param int $sort
     */
    public static function sortMultidimensionalArray(&$array, string $subfield, int $sort)
    {
        $sortarray = array();
        $arrayLength = count($array);
        $sortOrder = 1;
        if (!empty($array) && (isset($array))) {
            foreach ($array as $key => $row) {
                if ((!isset($row[$subfield]) || $row[$subfield] == '')) {
                    $row[$subfield] = $array[$key][$subfield] = $arrayLength;
                    $arrayLength++;
                }

                $sortarray[$key] =  $row[$subfield] ;
            }

            array_multisort($sortarray, $sort, $array);

            foreach ($array as $key => $row) {
                $array[$key][$subfield] = $sortOrder;
                $sortOrder++;
            }
        }
    }

    /**
     * Get country id from country_code
     * 
     * @param string $country_code
     *
     * @return string
     */
    public static function getCountryId(string $country_code)
    {
        $country = DB::table("country")->where("ISO", $country_code)->first();
        return $country->country_id;
    }

    /**
     * Get country detail from country_id
     * 
     * @param string $country_id
     *
     * @return mixed
     */
    public static function getCountry($country_id)
    {
        $country = DB::table("country")->where("country_id", $country_id)->first();
        $countryData = array('country_id' => $country->country_id,
                             'country_code' => $country->ISO,
                             'name' => $country->name,
                            );
         return $countryData;
    }

    /**
     * Get city data from city_id
     * 
     * @param string $city_id
     *
     * @return string
     */
    public static function getCity($city_id)
    {
        $city = DB::table("city")->where("city_id", $city_id)->first();
        $cityData = array('city_id' => $city->city_id,
                         'name' => $city->name
                        );
        return $cityData;
    }
}
