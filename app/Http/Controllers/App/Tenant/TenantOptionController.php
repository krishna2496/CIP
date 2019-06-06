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
        $data = $optionData = $slider = array(); 
        
        // Flag to check value is serialize or not
        $checkForSerialize = false;
        
        // Find custom data
        $tenantOptions = TenantOption::get(['option_name', 'option_value']);
        $data = $tenantOptions->toArray();
        
        if ($data) {
            foreach ($data as $key => $value) {
				// For slider
                if ($value['option_name'] == config('constants.TENANT_OPTION_SLIDER'))
					$slider[]= json_decode(@unserialize($value['option_value']),true);
                else 
					$optionData[$value['option_name']] = (@unserialize($value['option_value']) === false) ? $value['option_value'] : unserialize($value['option_value']);
				
            }

            // Sort an array by sort order of slider
            if(!empty($slider)){
				Helpers::sortMultidimensionalArray($slider, 'sort_order', SORT_ASC);
				$optionData['slider'] = $slider;
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
       
        if($tenantOptions && $tenantOptions->option_value){

            $tenantLogo = $tenantOptions->option_value;
        }
      
        return $tenantLogo;
    }
}
