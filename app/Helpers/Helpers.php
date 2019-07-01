<?php
namespace App\Helpers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use App\Traits\RestExceptionHandlerTrait;
use PDOException;
use Throwable;

class Helpers
{
    use RestExceptionHandlerTrait;
    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
    * It will return
    * @param Illuminate\Http\Request $request
    * @return string
    */
    public function getSubDomainFromRequest(Request $request) : string
    {
        try {
            if (env('APP_ENV')=='local') {
                return env('DEFAULT_TENANT');
            } else {
                return explode(".", parse_url($request->headers->all()['referer'][0])['host'])[0];
            }
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
     * @return mixed
     */
    public function getRefererFromRequest(Request $request)
    {
        try {
            if (isset($request->headers->all()['referer'])) {
                $parseUrl = parse_url($request->headers->all()['referer'][0]);
                return $parseUrl['scheme'].'://'.$parseUrl['host'].env('APP_PATH');
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
    public function sortMultidimensionalArray(&$array, string $subfield, int $sort)
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
     * @return Tenant
     */
    public function getTenantDetail(Request $request)
    {
        // Connect master database to get language details
        $this->switchDatabaseConnection('mysql', $request);

        $tenantName = $this->getSubDomainFromRequest($request);
        $tenant = DB::table('tenant')->where('name', $tenantName)->first();

        // Connect tenant database
        $this->switchDatabaseConnection('tenant', $request);
                
        return $tenant;
    }
    
    /**
     * Get country id from country_code
     *
     * @param string $country_code
     * @return int
     */
    public function getCountryId(string $country_code) : int
    {
        $country = DB::table("country")->where("ISO", $country_code)->first();
        return $country->country_id;
    }

    /**
     * Get country detail from country_id
     *
     * @param string $country_id
     * @return array
     */
    public function getCountry($country_id) : array
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
     * @return array
     */
    public function getCity($city_id) : array
    {
        $city = DB::table("city")->where("city_id", $city_id)->first();
        $cityData = array('city_id' => $city->city_id,
                         'name' => $city->name
                        );
        return $cityData;
    }

    /**
     * Switch database connection runtime
     *
     * @param string $connection
     * @param \Illuminate\Http\Request $request
     * @return void
     * @throws Exception
     */
    public function switchDatabaseConnection(string $connection, Request $request)
    {
        try {
            $domain = $this->getSubDomainFromRequest($request);
            // Set master connection
            $pdo = DB::connection('mysql')->getPdo();
            Config::set('database.default', 'mysql');

            if ($connection=="tenant") {
                // Uncomment code for production
                /*$tenant = DB::table('tenant')->where('name',$domain)->whereNull('deleted_at')->first();
                $this->createConnection($tenant->tenant_id);*/
                $pdo = DB::connection('tenant')->getPdo();
                Config::set('database.default', 'tenant');
            }
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    /**
     * Create database connection runtime
     *
     * @param int $tenantId
     */
    public function createConnection(int $tenantId)
    {
        try {
            Config::set('database.connections.tenant', array(
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'ci_tenant_'.$tenantId,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
            ));
            // Create connection for the tenant database
            $pdo = DB::connection('tenant')->getPdo();
            // Set default database
            Config::set('database.default', 'tenant');
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
