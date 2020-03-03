<?php
namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use App\Traits\RestExceptionHandlerTrait;
use Throwable;
use App\Exceptions\TenantDomainNotFoundException;
use Carbon\Carbon;
use stdClass;

class Helpers
{
    use RestExceptionHandlerTrait;
    
    /**
     * @var DB
     */
    private $db;

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->db = app()->make('db');
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
            if ((env('APP_ENV') === 'local' || env('APP_ENV') === 'testing')) {
                return env('DEFAULT_TENANT');
            } else {
                return parse_url($request->headers->all()['referer'][0])['host'];
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
        if (isset($request->headers->all()['referer'])) {
            $parseUrl = parse_url($request->headers->all()['referer'][0]);
            return $parseUrl['scheme'].'://'.$parseUrl['host'].env('APP_PATH');
        } else {
            return env('APP_MAIL_BASE_URL');
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
        $this->switchDatabaseConnection('mysql');
        $tenant = $this->db->table('tenant')->where('name', $tenantName)->whereNull('deleted_at')->first();
        // Connect tenant database
        $this->switchDatabaseConnection('tenant');

        return $tenant;
    }

    /**
     * Switch database connection runtime
     *
     * @param string $connection
     * @return void
     * @throws Exception
     */
    public function switchDatabaseConnection(string $connection)
    {
        // Set master connection
        $this->db->connection('mysql')->getPdo();

        $pdo = $this->db->connection('mysql')->getPdo();
        Config::set('database.default', 'mysql');

        if ($connection=="tenant") {
            $pdo = $this->db->connection('tenant')->getPdo();
            Config::set('database.default', 'tenant');
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
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'database' => 'ci_tenant_' . $tenantId,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ));
        // Create connection for the tenant database
        $pdo = $this->db->connection('tenant')->getPdo();
        // Set default database
        Config::set('database.default', 'tenant');
    }

    /**
     * Get date according to user timezone
     *
     * @param string $date
     * @return string
     */
    public function getUserTimeZoneDate(string $date): string
    {
        if (!($date instanceof Carbon)) {
            $date = Carbon::parse($date);
        }
        return $date->setTimezone(config('constants.TIMEZONE'))->format(config('constants.DB_DATE_TIME_FORMAT'));
    }

    /**
     * Get JWT token
     *
     * @param int $userId
     * @param string $tenantName
     * @return string
     */
    public static function getJwtToken(int $userId, string $tenantName) : string
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $userId, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 * 4, // Expiration time
            'fqdn' => $tenantName
        ];
        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Get tenant default profile image for user
     *
     * @param string $tenantName
     * @return string
     */
    public function getUserDefaultProfileImage(string $tenantName): string
    {
        $awsRegion = config('constants.AWS_REGION');
        $bucketName = config('constants.AWS_S3_BUCKET_NAME');
        $assetsFolder = config('constants.AWS_S3_ASSETS_FOLDER_NAME');
        $imagesFolder = config('constants.AWS_S3_IMAGES_FOLDER_NAME');
        $defaultProfileImage = config('constants.AWS_S3_DEFAULT_PROFILE_IMAGE');

        return 'https://s3.'.$awsRegion.'.amazonaws.com/'.$bucketName.'/'.$tenantName.'/'.$assetsFolder.
        '/'.$imagesFolder.'/'.$defaultProfileImage;
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
        $tenant = $this->db->table('tenant')->where('name', $tenantName)->first();
        if (is_null($tenant)) {
            throw new TenantDomainNotFoundException(
                trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND'),
                config('constants.error_codes.ERROR_TENANT_DOMAIN_NOT_FOUND')
            );
        }
        // Create database connection based on tenant id
        $this->createConnection($tenant->tenant_id);
        $pdo = $this->db->connection('tenant')->getPdo();
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
        $tenant = $this->getTenantDetail($request);
        // Connect master database to get tenant settings
        $this->switchDatabaseConnection('mysql');
        
        $tenantSetting = $this->db->table('tenant_has_setting')
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
        $this->switchDatabaseConnection('tenant');
        
        return $tenantSetting;
    }

    /**
     * Get domain from user API key
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getDomainFromUserAPIKeys(Request $request): string
    {
        // Check basic auth passed or not
        $this->switchDatabaseConnection('mysql');
        // authenticate api user based on basic auth parameters
        $apiUser = $this->db->table('api_user')
                    ->leftJoin('tenant', 'tenant.tenant_id', '=', 'api_user.tenant_id')
                    ->where('api_key', base64_encode($request->header('php-auth-user')))
                    ->where('api_user.status', '1')
                    ->where('tenant.status', '1')
                    ->whereNull('api_user.deleted_at')
                    ->whereNull('tenant.deleted_at')
                    ->first();

        $this->switchDatabaseConnection('tenant');
        return $apiUser->name;
    }

    /**
     * Change date format
     *
     * @param string $date
     * @param string $dateFormat
     * @return string
     */
    public function changeDateFormat(string $date, string $dateFormat): string
    {
        return date($dateFormat, strtotime($date));
    }

    /**
     * Convert in report time format
     *
     * @param string $totalHours
     * @return string
     */
    public function convertInReportTimeFormat(string $totalHours): string
    {
        $convertedHours = (int) ($totalHours / 60);
        $hours = $convertedHours . "h";
        $minutes = $totalHours % 60;
        return $hours . $minutes;
    }

    /**
     * Convert in report hours format
     *
     * @param string $totalHours
     * @return string
     */
    public function convertInReportHoursFormat(string $totalHours): string
    {
        $hours = (int) ($totalHours / 60);
        $minutes = ($totalHours % 60) / 60;
        $totalHours = $hours + $minutes;
        return number_format((float) $totalHours, 2, '.', '');
    }

    /**
     * Trim text after x words
     *
     * @param string $phrase
     * @param int maxWords
     * @return null|string
     */
    public function trimText(string $phrase, int $maxWords)
    {
        $phrase_array = explode(' ', $phrase);
        if (count($phrase_array) > $maxWords && $maxWords > 0) {
            $phrase = implode(' ', array_slice($phrase_array, 0, $maxWords)).'...';
        }
        return $phrase;
    }

    /**
     * Get tenant default assets url
     *
     * @param string $tenantName
     * @return string
     */
    public function getAssetsUrl(string $tenantName): string
    {
        return 'https://s3.'.config('constants.AWS_REGION').'.amazonaws.com/'.
        config('constants.AWS_S3_BUCKET_NAME').'/'.$tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').
        '/'.config('constants.AWS_S3_IMAGES_FOLDER_NAME').'/';
    }

    /**
     * Get language details
     * @param int $languageId
     * @return Object
     */
    public function getLanguageDetail(int $languageId): ?Object
    {
        $this->switchDatabaseConnection('mysql');
        $language = $this->db->table('language')->where('language_id', $languageId)->whereNull('deleted_at')->first();
        $this->switchDatabaseConnection('tenant');
        return $language;
    }
	
	/**
     * Remove unwanted characters from json
     * @param string $filePath
     * @return string
     */
    public function removeUnwantedCharacters(string $filePath): string
    {
        $jsonFileContent = file_get_contents($filePath);

		// This will remove unwanted characters.
		for ($i = 0; $i <= 31; ++$i) { 
			$jsonFileContent = str_replace(chr($i), "", $jsonFileContent); 
		}
		$jsonFileContent = str_replace(chr(127), "", $jsonFileContent);

		// This is the most common part
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters 
		if (0 === strpos(bin2hex($jsonFileContent), 'efbbbf')) {
		   $jsonFileContent = substr($jsonFileContent, 3);
		}
		
		return $jsonFileContent;
    }
}
