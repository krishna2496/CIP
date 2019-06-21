<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use DB;

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
     * It will retrive tenant details from tenant table
     *
     * @param array $array
     * @param Illuminate\Http\Request $request
     */
    public static function getTenantDetail(Request $request)
    {
        // Connect master database to get language details
        DatabaseHelper::switchDatabaseConnection('mysql', $request);

        $tenantName = Self::getSubDomainFromRequest($request);
        $tenant = DB::table('tenant')->where('name', $tenantName)->first();

        // Connect tenant database
        DatabaseHelper::switchDatabaseConnection('tenant', $request);
                
        return $tenant;
    }
}
