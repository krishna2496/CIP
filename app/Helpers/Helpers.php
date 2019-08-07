<?php
namespace App\Helpers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use DB;
use App\Traits\RestExceptionHandlerTrait;
use PDOException;
use Throwable;
use App\Exceptions\TenantDomainNotFoundException;
use Carbon\Carbon;

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
        if ((env('APP_ENV') == 'local' || env('APP_ENV') == 'testing')) {
            return env('DEFAULT_TENANT');
        } else {
            if (isset($request->headers->all()['referer'])) {
                try {
                    return explode(".", parse_url($request->headers->all()['referer'][0])['host'])[0];
                } catch (\Exception $e) {
                    return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
                }
            } else {
                if ((env('APP_ENV')=='local' || env('APP_ENV')=='testing')) {
                    return env('DEFAULT_TENANT');
                } else {
                    throw new TenantDomainNotFoundException(
                        trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND'),
                        config('constants.error_codes.ERROR_TENANT_DOMAIN_NOT_FOUND')
                    );
                }
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
            // error unable to find domain referer
            throw new \Exception(trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND'));
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
     * @param Illuminate\Http\Request $request
     * @return object $tenant
     */
    public function getTenantDetail(Request $request): object
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
     * @param int  $country_id
     * @return array
     */
    public function getCountry(int $country_id) : array
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
     * @param int $city_id
     * @return array
     */
    public function getCity(int $city_id) : array
    {
        $city = DB::table("city")->whereIn("city_id", explode(",", $city_id))->get()->toArray();
        $cityData = [];
        if (!empty($city)) {
            foreach ($city as $key => $value) {
                $cityData[$value->city_id] = $value->name;
            }
        }
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

    /**
     * Get date according to user timezone
     *
     * @param string $date
     * @return string
     */
    public function getUserTimeZoneDate(string $date) : string
    {
        if (config('constants.TIMEZONE') != '' && $date !== null) {
            if (!($date instanceof Carbon)) {
                if (is_numeric($date)) {
                    // Assume Timestamp
                    $date = Carbon::createFromTimestamp($date);
                } else {
                    $date = Carbon::parse($date);
                }
            }
            return $date->setTimezone(config('constants.TIMEZONE'))->format(config('constants.DB_DATE_FORMAT'));
        }
        return $date;
    }

    /**
     * Check url extension
     *
     * @param string $url
     * @param string $type
     * @return bool
     */
    public function checkUrlExtension(string $url, string $type) : bool
    {
        $urlExtension = pathinfo($url, PATHINFO_EXTENSION);
        $constants = ($type == config('constants.IMAGE')) ? config('constants.image_types')
        : config('constants.document_types');
        return (!in_array($urlExtension, $constants)) ? false : true;
    }

    /**
     * Get JWT token
     *
     * @param int $userId
     * @return string
     */
    public static function getJwtToken(int $userId) : string
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $userId, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 * 4, // Expiration time
            'fqdn' => env('DEFAULT_TENANT')
        ];
        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Get tenant default profile image for user
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getDefaultProfileImage(Request $request): string
    {
        $tenantName = $this->getSubDomainFromRequest($request);

        return 'https://s3.'.config('constants.AWS_REGION').'.amazonaws.com/'.
        config('constants.AWS_S3_BUCKET_NAME').'/'.$tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').
        '/'.config('constants.AWS_S3_IMAGES_FOLDER_NAME').'/'.config('constants.AWS_S3_DEFAULT_PROFILE_IMAGE');
    }
}
