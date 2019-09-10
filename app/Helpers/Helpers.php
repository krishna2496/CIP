<?php
namespace App\Helpers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use DB;
use App\Traits\RestExceptionHandlerTrait;
use Throwable;
use App\Exceptions\TenantDomainNotFoundException;
use Carbon\Carbon;
use stdClass;
use Illuminate\Support\Facades\Hash;

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
    * It will return tenant name from request
    * @param Illuminate\Http\Request $request
    * @return string
    */
    public function getSubDomainFromRequest(Request $request) : string
    {
        // Check admin request
        if ($request->header('php-auth-pw') && $request->header('php-auth-user')) {
            return $this->getDomainFromUserAPIKeys($request);
        } else {
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
     * It will retrive tenant details from tenant table
     *
     * @param Illuminate\Http\Request $request
     * @return object $tenant
     */
    public function getTenantDetail(Request $request): object
    {
        // Connect master database to get language details
        $tenantName = $this->getSubDomainFromRequest($request);
        $this->switchDatabaseConnection('mysql', $request);
        $tenant = DB::table('tenant')->where('name', $tenantName)->whereNull('deleted_at')->first();
        // Connect tenant database
        $this->switchDatabaseConnection('tenant', $request);
                
        return $tenant;
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
            // Set master connection
            $pdo = DB::connection('mysql')->getPdo();
            Config::set('database.default', 'mysql');

            if ($connection=="tenant") {
                $pdo = DB::connection('tenant')->getPdo();
                Config::set('database.default', 'tenant');
            }
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
            return $date->setTimezone(config('constants.TIMEZONE'))->format(config('constants.DB_DATE_TIME_FORMAT'));
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

    /**
     * Get tenant details from tenant name only
     *
     * @param string $tenantName
     * @return stdClass $tenant
     */
    public function getTenantDetailsFromName(string $tenantName): stdClass
    {
        // Get tenant details based on tenant name
        $tenant = DB::table('tenant')->where('name', $tenantName)->first();
        if (is_null($tenant)) {
            throw new TenantDomainNotFoundException(
                trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND'),
                config('constants.error_codes.ERROR_TENANT_DOMAIN_NOT_FOUND')
            );
        }
        // Create database connection based on tenant id
        $this->createConnection($tenant->tenant_id);
        $pdo = DB::connection('tenant')->getPdo();
        Config::set('database.default', 'tenant');

        return $tenant;
    }

    /**
     * Get fetch all tenant settings detais
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function getAllTenantSetting(Request $request)
    {
        try {
            $tenant = $this->getTenantDetail($request);
            // Connect master database to get tenant settings
            $this->switchDatabaseConnection('mysql', $request);
            
            $tenantSetting = DB::table('tenant_has_setting')
            ->select(
                'tenant_has_setting.tenant_setting_id',
                'tenant_setting.key',
                'tenant_setting.tenant_setting_id',
                'tenant_setting.description',
                'tenant_setting.title'
            )
            ->leftJoin(
                'tenant_setting',
                'tenant_setting.tenant_setting_id',
                '=',
                'tenant_has_setting.tenant_setting_id'
            )
            ->whereNull('tenant_has_setting.deleted_at')
            ->whereNull('tenant_setting.deleted_at')
            ->where('tenant_id', $tenant->tenant_id)
            ->orderBy('tenant_has_setting.tenant_setting_id')
            ->get();

            // Connect tenant database
            $this->switchDatabaseConnection('tenant', $request);
            
            return $tenantSetting;
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    public function getDomainFromUserAPIKeys(Request $request)
    {
        // Check basic auth passed or not
        $this->switchDatabaseConnection('mysql', $request);
        // authenticate api user based on basic auth parameters
        $apiUser = DB::table('api_user')
                    ->leftJoin('tenant', 'tenant.tenant_id', '=', 'api_user.tenant_id')
                    ->where('api_key', base64_encode($request->header('php-auth-user')))
                    ->where('api_user.status', '1')
                    ->where('tenant.status', '1')
                    ->whereNull('api_user.deleted_at')
                    ->whereNull('tenant.deleted_at')
                    ->first();

        $this->switchDatabaseConnection('tenant', $request);
        // If user authenticates successfully
        if ($apiUser && Hash::check($request->header('php-auth-pw'), $apiUser->api_secret)) {
            // Create connection with their tenant database
            return $apiUser->name;
        } else {
            throw new TenantDomainNotFoundException(
                trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND'),
                config('constants.error_codes.ERROR_TENANT_DOMAIN_NOT_FOUND')
            );
        }
    }
}
