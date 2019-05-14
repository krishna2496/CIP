<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $apiData = '';
    protected $apiCode = 500;
    protected $apiStatus = false;
    protected $apiMessage = '';
    protected $pageLimit = 10;

    public function __construct()
    {
        // Init basic parameters
        $this->page = Input::get('page', 1);
        $this->pageLimit = Input::get('limit', 10);
        $this->apiData = new \stdClass();
    }

    protected function response()
    {
        $response['data'] = $this->apiData;
        $response['code'] = $this->apiCode;
        $response['status'] = $this->apiStatus;
        $response['message'] = $this->apiMessage;

        return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
    }
}
