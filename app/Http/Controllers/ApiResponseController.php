<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiResponseController extends Controller
{
    protected $apiData = '';
    protected $apiErrorCode = 500;
    protected $apiStatus = '';
    protected $apiMessage = '';
    protected $pageLimit = 10;
    protected $errorType = '';

    public function __construct()
    {
        // Init basic parameters
        $this->page = Input::get('page', 1);
        $this->pageLimit = Input::get('limit', 10);
        $this->apiData = new \stdClass();
    }
    
    /**
     * Prepare success response
	 * @return mixed
     */
    protected function response()
    {
        $response['status'] = $this->apiStatus;
        if(!empty((array)$this->apiData) && $this->apiData != '')
            $response['data'] = $this->apiData;
        
        // Check response data have pagination or not? Pagination response parameter sets
        if((is_object($this->apiData)) && ($this->apiData) && get_class($this->apiData) == "Illuminate\Pagination\LengthAwarePaginator"){            
            $response['data'] = $this->apiData->toArray()['data'];
            $response['pagination'] = [
                "total" => $this->apiData->total(),
                "per_page" => $this->apiData->perPage(),
                "current_page" => $this->apiData->currentPage(),
                "total_pages" => $this->apiData->lastPage(),
                "next_url" => $this->apiData->nextPageUrl()
            ];
            $this->apiStatus = 200;
        }
        if($this->apiMessage)
            $response['message'] = $this->apiMessage;

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
    protected function errorResponse($status_code, $status_type, $custom_error_code, $custom_error_message)
    {
       
        $response['status'] = $status_code;
        $response['type'] = $status_type;
        $response['code'] = $custom_error_code;
        $response['message'] = $custom_error_message;
        $data["errors"][] = $response;
       
        return response()->json($data, $status_code, [], JSON_NUMERIC_CHECK);
    }
    
}