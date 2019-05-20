<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use App\TenantOption;

class TenantOptionController extends ApiController
{
    
    /**
     * Custom data like style,logo and text for login screen etc..
     *  
     * @return mixed
     */
    public function getTenantOption() 
    { 
        $data = $dataResponse = array(); 
        $checkForSerialize = FALSE;
        // find custom data
        $tenantOptions = TenantOption::get(['option_name', 'option_value'])->where('deleted_at', NULL);
        $data = $tenantOptions->toArray();
        //if data exist
        if($data) {
            foreach ($data as $key =>$value) {
                //check if value is serialize or not
                $checkForSerialize = @unserialize($value['option_value']);
                
                if ($checkForSerialize === FALSE) { // if not serialize value
                    $dataResponse[$value['option_name']] = $value['option_value'];
                }else { // for serialize value
                    $dataResponse[$value['option_name']] = unserialize($value['option_value']);
                }
                
            }
        }
        
        $this->apiData = $dataResponse;
        $this->apiCode = app('Illuminate\Http\Response')->status();
        $this->apiStatus = true;
        $this->apiMessage = 'Tenant options listing successfully';
        return $this->response();
        
    }
}
