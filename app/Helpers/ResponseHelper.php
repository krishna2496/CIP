<?php

namespace App\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseHelper
{
    /**
     * Prepare success response
     * 
     * @param int $apiStatus
     * @param string $apiMessage     
     * @param Model Object $apiData
     * @return mixed
     */
    public static function success(string $apiStatus = '', string $apiMessage = '', array $apiData = [])
    {

        $response['status'] = $apiStatus;
        
        if(!empty($apiData))
            $response['data'] = $apiData;

        if($apiMessage)
            $response['message'] = $apiMessage;
            
        return response()->json($response, $apiStatus, [], JSON_NUMERIC_CHECK);
    }
	
	/**
     * Prepare success response
     * 
     * @param int $apiStatus
     * @param string $apiMessage     
     * @param Model Object $apiData
     * @return mixed
     */
    public static function successWithPagination(string $apiStatus = '', string $apiMessage = '', LengthAwarePaginator $apiData)
    {

        $response['status'] = $apiStatus;
        
        // Check response data have pagination or not? Pagination response parameter sets
		if($apiData->count()){
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
            
        return response()->json($response, $apiStatus, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Prepare error response
     * 
	 * @param int $statusCode
     * @param string $statusType
     * @param int $customErrorCode
     * @param string $customErrorMessage
	 * @return mixed
     */
    public static function error(string $statusCode = '', string $statusType = '', string $customErrorCode = '', string $customErrorMessage = '')
    {
       
        $response['status'] = $statusCode;
        $response['type'] = $statusType;
        if ($customErrorCode) $response['code'] = $customErrorCode;
        $response['message'] = $customErrorMessage;
        $data["errors"][] = $response;
       
        return response()->json($data, $statusCode, [], JSON_NUMERIC_CHECK);
    }
}
