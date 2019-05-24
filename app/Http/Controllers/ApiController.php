<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
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
     * success response format
     */
    protected function response()
    {
        $response['status'] = $this->apiStatus;
        if(!empty((array)$this->apiData) && $this->apiData != '')
            $response['data'] = $this->apiData;
        
        // Check response data have pagination or not? Pagination response parameter sets
        if((is_object($this->apiData)) &&($this->apiData) && get_class($this->apiData) == "Illuminate\Pagination\LengthAwarePaginator"){            
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
     * error response format
     */
    protected function errorResponse()
    {
       
        $response['type'] = $this->errorType;
        $response['status'] = $this->apiStatus;
        $response['code'] = $this->apiErrorCode;
        $response['message'] = $this->apiMessage;
        $data["errors"][] = $response;
       
        return response()->json($data, $this->apiStatus, [], JSON_NUMERIC_CHECK);
    }
    
}