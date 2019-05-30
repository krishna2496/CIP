<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use App\TenantOption;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;

class TenantOptionController extends Controller
{
    
    /**
     * Get tenant options from table `tenant_options`
     *  
     * @return mixed
     */
    public function getTenantOption() 
    {
        $data = $optionData = array(); 
        
        //flag to check value is serialize or not
        $checkForSerialize = FALSE;
        
        // find custom data
        $tenantOptions = TenantOption::get(['option_name', 'option_value'])->where('deleted_at', NULL);
        $data = $tenantOptions->toArray();
        
        //if data exist
        if ($data) {
            foreach ($data as $key =>$value) {
                //check if value is serialize or not
                $checkForSerialize = @unserialize($value['option_value']);
                
                if ($checkForSerialize === FALSE) {
                    // if not serialize value
                    $optionData[$value['option_name']] = $value['option_value'];
                } else {
                    // for serialize value
                    $optionData[$value['option_name']] = unserialize($value['option_value']);
                }
            }
        }
        return Helpers::response(app('Illuminate\Http\Response')->status(), '', $optionData);
    }

    /**
     * Get tenant logo from table `tenant_options`
     *  
     * @return string
     */
    public function getTenantLogo() 
    {
        $tenantLogo = ''; 
        
        // find custom data
        $tenantOptions = TenantOption::get(['option_name', 'option_value'])->where('deleted_at', NULL)->where('option_name','custom_logo')->first();
       
        if($tenantOptions->option_value){
            $tenantLogo = $tenantOptions->option_value;
        }
        dd($tenantLogo);
        return $tenantLogo;
    }


}
