<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    /**
     * returned data in api response
     *
     * @var mixed
     */
    protected $apiData = '';
    
    /**
     * returned code in api response
     *
     * @var integer
     */
    protected $apiCode = 500;
    
    /**
     * returned status in api response
     *
     * @var string
     */
    protected $apiStatus = false;
    
    /**
     * returned message in api response
     *
     * @var string
     */
    protected $apiMessage = '';
    
    /**
     * pagination limit in data listing
     *
     * @var integer
     */
    protected $pageLimit = 10;
    
    /**
     * error type in api response
     *
     * @var string
     */
    protected $errorType = '';

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct()
    {
        // Init basic parameters
        $this->page = Input::get('page', 1);
        $this->pageLimit = Input::get('limit', 10);
        $this->apiData = new \stdClass();
    }
    
    /**
     * Api success response.
     * 
     * @return mixed
     */
    protected function response()
    {
        // Check response data have pagination or not? Pagination response parameter sets
      
        if ((is_object($this->apiData)) && ($this->apiData) && get_class($this->apiData) == "Illuminate\Pagination\LengthAwarePaginator") {            
            $response['data'] = $this->apiData->toArray()['data'];
            $response['pagination'] = [
                "total" => $this->apiData->total(),
                "per_page" => $this->apiData->perPage(),
                "current_page" => $this->apiData->currentPage(),
                "total_pages" => $this->apiData->lastPage(),
                "next_url" => $this->apiData->nextPageUrl()
            ];
            $this->apiCode = 200;
            $this->apiStatus = true;
        } else {
            $response['data'] = $this->apiData;
        }
        
        $response['code'] = $this->apiCode;
        $response['status'] = $this->apiStatus;
        $response['message'] = $this->apiMessage;

        return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
    }
    
    /**
     * Api error response.
     * 
     * @return mixed
     */
    protected function errorResponse()
    {
        $response['type'] = $this->errorType;
        $response['status'] = $this->apiStatus;
        $response['code'] = $this->apiCode;
        $response['message'] = $this->apiMessage;
        $data["errors"][] = $response;
       
        return response()->json($data, 400, [], JSON_NUMERIC_CHECK);
    }
    
}
