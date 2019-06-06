<?php

namespace App\Http\Controllers\Admin\User;

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
		$customFields = UserCustomField::all();
		$customFieldsData = $customFields->toArray();          
        
        if (empty($customFieldsData)) {
        	// Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_NO_DATA_FOUND');
            return Helpers::response($apiStatus, $apiMessage);
        }
        $data = array();
        $detail = array();
        foreach ($customFieldsData as $value) {
        	$detail[] = array('field_id' => $value['field_id'],
							  'name' => $value['name'],
							  'type' => $value['type'],
							  'is_mandatory' => $value['is_mandatory'],
							  'translation' => (@unserialize($value['translations']) === false) ? $value['translations'] : unserialize($value['translations']));
        }
        // Set response data
        $apiData = $detail;
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = trans('api_success_messages.success_message.MESSAGE_USER_LIST_SUCCESS');
        return Helpers::response($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Store user custom field
     *
     * @param \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {       
        // Server side validataions
        $validator = Validator::make($request->toArray(), ["name" => "required", 
															"type" => ['required', Rule::in(config('constants.custom_field_types'))], 
															"is_mandatory" => "required", 
															"translation" => "required"]);
        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20102'),
                                        $validator->errors()->first());
        }   
        try {
			
			$translation = $request->translation;      
	
			if ((($request->type == config('constants.custom_field_types.DROP-DOWN') ) || ($request->type == config('constants.custom_field_types.RADIO'))) && 
				(empty($translation[0]['values']))) {
				// Set response data if values are null for Drop-down and radio type
				return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                trans('api_error_messages.custom_error_code.ERROR_20026'),
                                trans('api_error_messages.custom_error_message.20026'));
			} 
			
            // Set data for create new record
            $customFieldData = array('name' => $request->name, 
									 'type' => $request->type, 
									 'is_mandatory' => $request->is_mandatory, 
									 'translations' => serialize($translation));
            
			// Create new user custom field record 
            UserCustomField::create($customFieldData);
			
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CUSTOM_FIELD_ADD_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);
        
        } catch (\Exception $e) {
            // Any other error occured when trying to insert data into database.
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'), 
                                    trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'), 
                                    trans('api_error_messages.custom_error_code.ERROR_20004'), 
                                    trans('api_error_messages.custom_error_message.20004'));
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update user custom field
     *
     * @param \Illuminate\Http\Request  $request
     * @param int  $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $fieldData = UserCustomField::find($id);
        if (!$fieldData) {
        	return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20018'),
                                        trans('api_error_messages.custom_error_message.20032'));
        } 
        // Server side validataions
        $validator = Validator::make($request->toArray(), ["name" => "required", 
                                                            "type" => ['required', Rule::in(config('constants.custom_field_types'))], 
                                                            "is_mandatory" => "required", 
                                                            "translation" => "required" 
        ]); 
        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20018'),
                                        $validator->errors()->first());
        } 
        try {   
            $translation = $request->translation; 
            if ((($request->type == config('constants.custom_field_types.DROP-DOWN') ) || ($request->type == config('constants.custom_field_types.RADIO'))) && 
                    (empty($translation[0]['values']))) {
                // Set response data if values are null for Drop-down and radio type
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                trans('api_error_messages.custom_error_code.ERROR_20026'),
                                trans('api_error_messages.custom_error_message.20026'));
            } 
                                  
            // Set data for update record
            $customFieldData = array('name' => $request->name, 
                                    'type' => $request->type, 
                                    'is_mandatory' => $request->is_mandatory, 
                                    'translations' => serialize($translation)
                                );
            // Update user custom field
            UserCustomField::where('field_id', $id)->update($customFieldData);
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CUSTOM_FIELD_UPDATE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);               
        } catch (\Exception $e) { 
            // Any other error occured when trying to update data into database.
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'), 
                                    trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'), 
                                    trans('api_error_messages.custom_error_code.ERROR_20004'), 
                                    trans('api_error_messages.custom_error_message.20004'));
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $userField = UserCustomField::findorFail($id);
            $userField->delete();

            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();            
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CUSTOM_FIELD_DELETE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);
            
        } catch(\Exception $e){
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_403'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('api_error_messages.custom_error_code.ERROR_20028'), 
                                        trans('api_error_messages.custom_error_message.20028'));

        }
    }

    /**
     * Handle error if id is not passed in url
     *
     * @return mixed
     */
    public function handleError()
    {
        return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_400'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_400'), 
                                        trans('api_error_messages.custom_error_code.ERROR_20034'), 
                                        trans('api_error_messages.custom_error_message.20034'));
    }
}
