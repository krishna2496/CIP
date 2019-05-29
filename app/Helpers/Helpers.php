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
    public static function getSubDomainFromRequest(Request $request)
    {
    	try{    	
        	return explode(".",parse_url($request->headers->all()['referer'][0])['host'])[0];
        } catch (\Exception $e) {
        	return $e->getMessage();
        }

    }

    /**
     * Prepare success response
     * 
     * @param Model Object $apiData
     * @param int $apiStatus
     * @param string $apiMessage     
     * @return mixed
     */
    public static function response($apiData, $apiStatus = 200, $apiMessage = '')
    {

        $response['status'] = $apiStatus;
        
        if(!empty((array)$apiData) && $apiData != '')
            $response['data'] = $apiData;

        // Check response data have pagination or not? Pagination response parameter sets
        if((is_object($apiData)) && ($apiData) && get_class($apiData) == "Illuminate\Pagination\LengthAwarePaginator"){            
            $response['data'] = $apiData->toArray()['data'];
            $response['pagination'] = [
                "total" => $apiData->total(),
                "per_page" => $apiData->perPage(),
                "current_page" => $apiData->currentPage(),
                "total_pages" => $apiData->lastPage(),
                "next_url" => $apiData->nextPageUrl()
            ];
        }
        if($apiMessage)
            $response['message'] = $apiMessage;
            
        return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
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
