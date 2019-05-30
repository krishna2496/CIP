<?php

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class Helpers
{
    /**
     * Prepare success response
     * 
     * @param int $apiStatus
     * @param string $apiMessage     
     * @param Model Object $apiData
     * @return mixed
     */
    public static function response($apiStatus = 200, $apiMessage = '', $apiData = '')
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
