<?php

namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\UserCustomField;
use App\Helpers\Helpers;
use Illuminate\Validation\Rule;
use Validator;

class UserCustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userFieldQuery = UserCustomField::select('field_id', 'name', 'type', 'is_mandatory', 'translations')
        ->whereNull('deleted_at');

        try {
            $userFieldList = $userFieldQuery->get()->toArray();            
        } catch(\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_403'), 
                                        config('errors.custom_error_code.ERROR_40018'), 
                                        config('errors.custom_error_message.40018'));           
        }

        if (empty($userFieldList)) {
        	// Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_NO_DATA_FOUND');
            return Helpers::response($apiStatus, $apiMessage);
        }
        $data = array();
        $detail = array();
        foreach ($userFieldList as $value) {
        	//flag to check value is serialize or not
    		$checkForSerialize = false;
    		//check if value is serialize or not
            $checkForSerialize = @unserialize($value['translations']);                
            if ($checkForSerialize === false) 
                $translations = $value['translations'];
            else 
                $translations = unserialize($value['translations']);
            
            $data['field_id'] = $value['field_id'];
            $data['name'] = $value['name'];
            $data['type'] = $value['type'];
            $data['is_mandatory'] = $value['is_mandatory'];
            $data['translation'] = $translations;
            $detail[] = $data;
        }
        // Set response data
        $apiData = $detail;
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = config('messages.success_message.MESSAGE_USER_LIST_SUCCESS');
        return Helpers::response($apiStatus, $apiMessage, $apiData);
    }
}
