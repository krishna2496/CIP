<?php

namespace App\Helpers;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use DB;
use App;

class Helpers
{
	/**
	* It will return 
	* @param Illuminate\Http\Request $request
	* @return string
	*/
    public static function getSubDomainFromRequest(Request $request)
    {
    	try{    	
        	return explode(".",parse_url($request->headers->all()['referer'][0])['host'])[0];
        } catch (\Exception $e) {
            if(env('APP_ENV')=='local')
                return env('DEFAULT_TENANT');
            else
        	   return $e->getMessage();
        }

    }

    /**
     * Prepare error response
     * 
     * @param int $status_code
     * @param string $status_type
     * @param int $custom_error_code
     * @param string $custom_error_message
     * @return mixed
     */ 
    public static function getRefererFromRequest(Request $request)
    {
    	try{
    		if(isset($request->headers->all()['referer'])){    	
        		$parseUrl = parse_url($request->headers->all()['referer'][0]);
        		return $parseUrl['scheme'].'://'.$parseUrl['host'].':'.$parseUrl['port'];
        	}else{
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
    public static function sortMultidimensionalArray(&$array, $subfield, $sort) {
        $sortarray = array();
        $arrayLength = count($array);
        $sortOrder = 1;
        if(!empty($array) && (isset($array))){

            foreach ($array as $key => $row) {
                
                if((!isset($row[$subfield]) || $row[$subfield] == '')){
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
     * Switch database connection runtime
     * 
     * @param int $connection
     * @param mixed $request
     *
     * @return string
     */
    public static function switchDatabaseConnection($connection, $request = '')
    {
        $domain = Self::getSubDomainFromRequest($request);
        // Set master connection         
        $pdo = DB::connection('mysql')->getPdo();
        Config::set('database.default', 'mysql');

        if($connection=="tenant"){
            // Set tenant connection 
            /*$tenant = DB::table('tenant')->where('name',$domain)->whereNull('deleted_at')->first();
            $this->createConnection($tenant);*/
            $pdo = DB::connection('tenant')->getPdo();
            Config::set('database.default', 'tenant');
        }
        return;
    }
	
    public function createConnection($tenant)
    {        
        Config::set('database.connections.tenant', array(
            'driver'    => 'mysql',
            'host'      => env('DB_HOST'),
            'database'  => 'ci_tenant_'.$tenant->tenant_id,
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
        ));        
        try {
            // Create connection for the tenant database
            $pdo = DB::connection('tenant')->getPdo();
            // Set default database
            Config::set('database.default', 'tenant');
        } catch (\PDOException $e) {
            if ($e instanceof \PDOException) {            
                return Helpers::errorResponse(trans('messages.status_code.HTTP_STATUS_FORBIDDEN'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('messages.custom_error_code.ERROR_41000'), 
                                        trans('messages.custom_error_message.41000'));
            }
        }        
    }
    public function downloadImage($origin)
    {
        $filename = basename($origin);
        $saveto   = App::basePath().'\public\tmp\\'.$filename;

        $ch = curl_init ($origin);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXY, '192.168.10.5:8080');
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        
        $raw=curl_exec($ch);
        curl_close ($ch);

        if(file_exists($saveto)){
            unlink($saveto);
        }
        
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);

        return $filename;
    }    

    /**
     * Upload file on AWS s3 bucket
     * 
     * @param string $url
     * @param string $tenantName
     *
     * @return string
     */
    public static function uploadFileOnS3Bucket($url, $tenantName)
    {
        try{
            $disk = Storage::disk('s3');
            // Comment $context_array and $context code before going live
            $context_array = array('http'=>array('proxy'=>'192.168.10.5:8080','request_fulluri'=>true));
            $context = stream_context_create($context_array);            
            // Comment below line before going live
            $disk->put($tenantName.'/'.basename($url), file_get_contents($url, false, $context));
            // Uncomment below line before going live
            // $disk->put($tenantName.'/'.basename($url), file_get_contents($url));          
            $file = $disk->get($tenantName.'/'.basename($url));
           
            $pathInS3 = 'https://s3.'.env("AWS_REGION").'.amazonaws.com/' . env("AWS_S3_BUCKET_NAME") . '/'.$tenantName.'/'.basename($url);
            return $pathInS3;

        } catch(\Exception $e) {
            return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_FORBIDDEN'), 
                                        trans('messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('messages.custom_error_code.ERROR_40022'), 
                                        trans('messages.custom_error_message.40022'));
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
