<?php

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class Helpers
{
	/**
	* It will return 
	* @param Illuminate\Http\Request $request
	* @return string
	*/
    public static function getSubDomainFromUrl(Request $request)
    {
    	try{    	
        	return explode(".",parse_url($request->headers->all()['referer'][0])['host'])[0];
        } catch (\Exception $e) {
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
     * Prepare error response
     * 
	 * @param int $status_code
     * @param string $status_type
     * @param int $custom_error_code
     * @param string $custom_error_message
	 * @return mixed
     */
    public static function errorResponse($status_code, $status_type, $custom_error_code, $custom_error_message)
    {
       
        $response['status'] = $status_code;
        $response['type'] = $status_type;
        $response['code'] = $custom_error_code;
        $response['message'] = $custom_error_message;
        $data["errors"][] = $response;
       
        return response()->json($data, $status_code, [], JSON_NUMERIC_CHECK);
    }

}
