<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use App\TenantOption;

class CustomController extends ApiController
{
    
    /**
     * Custom data like style,logo and text for login screen etc..
     *  
     * @return mixed
     */
    public function customData() 
    { 
        
        $tenantOptions = TenantOption::get(['option_name', 'option_value'])->where(['deleted_at'=>NULL]);
        echo '<pre>';
        print_r($tenantOptions->toArray());exit;
        $this->apiData = $tenantOptions;
       
        return $this->response();
        
    }
}
