<?php

namespace App\Http\Controllers\Admin\Tenant;

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

        if (count($userFieldList)>0) {
            $data = array();
            $detail = array();
            foreach ($userFieldList as $value) {
                $data['field_id'] = $value['field_id'];
                $data['name'] = $value['name'];
                $data['type'] = $value['type'];
                $data['is_mandatory'] = $value['is_mandatory'];
                $data['translation'] = json_decode($value['translations']);
                $detail[] = $data;
            }
            // Set response data
            $apiData = $detail;
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_USER_LIST_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage, $apiData);
        } else {
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_NO_DATA_FOUND');
            return Helpers::response($apiStatus, $apiMessage);
        }
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
        $validator = Validator::make($request->toArray(), ["name" => "required", "type" => ['required', Rule::in(['Text', 'Email', 'Drop-down', 'radio'])], "is_mandatory" => "required", "translation" => "required" 
        ]);
        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20018'),
                                        $validator->errors()->first());
        }   
        try {           
            $translation = $request->translation;
            if ((($request->type == 'Drop-down' ) || ($request->type == 'radio')) && ($translation['values'] == "")) {
                // Set response data if values are null for Drop-down and radio type
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                    config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                    config('errors.custom_error_code.ERROR_20026'),
                                    config('errors.custom_error_message.20026'));
            } else {                    
                // Set data for create new record
                $insert = array( 'name' => $request->name, 'type' => $request->type, 'is_mandatory' => $request->is_mandatory, 'translations' => json_encode($translation));
                // Create new user custom field
                $insertData = UserCustomField::create($insert);
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = config('messages.success_message.MESSAGE_CUSTOM_FIELD_ADD_SUCCESS');
                return Helpers::response($apiStatus, $apiMessage);
            }
        } catch (\Exception $e) {
            // Any other error occured when trying to insert data into database.
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                    config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                    config('errors.custom_error_code.ERROR_20004'), 
                                    config('errors.custom_error_message.20004'));
            
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
        if ($fieldData) { 
            // Server side validataions
            $validator = Validator::make($request->toArray(), ["name" => "required", "type" => ['required', Rule::in(['Text', 'Email', 'Drop-down', 'radio'])], "is_mandatory" => "required", "translation" => "required" 
            ]);
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                            config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                            config('errors.custom_error_code.ERROR_20018'),
                                            $validator->errors()->first());
            } 

            try {           
                // Get data for options
                $translation = $request->translation;
                if ((($request->type == 'Drop-down' ) || ($request->type == 'radio')) && ($translation['values'] == "")) {
                    // Set response data if values are null for Drop-down and radio
                    return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20026'),
                                        config('errors.custom_error_message.20026'));
                } else {                    
                    // Set data for update record
                    $update = array();
                    $update['name'] = $request->name;
                    $update['type'] = $request->type;
                    $update['is_mandatory'] = $request->is_mandatory;
                    $update['translations'] = json_encode($translation);
                    // Update user custom field
                    $updateData = UserCustomField::where('field_id', $id)->update($update);
                    // Set response data
                    $apiStatus = app('Illuminate\Http\Response')->status();
                    $apiMessage = config('messages.success_message.MESSAGE_CUSTOM_FIELD_UPDATE_SUCCESS');
                    return Helpers::response($apiStatus, $apiMessage);
                }
            } catch (\Exception $e) {
                // Any other error occured when trying to update data into database.
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_20004'), 
                                        config('errors.custom_error_message.20004'));
            }
        } else {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20018'),
                                        config('errors.custom_error_message.20018'));
        }          
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $userField = UserCustomField::findorFail($id);
            $userField->delete();

            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();            
            $apiMessage = config('messages.success_message.MESSAGE_CUSTOM_FIELD_DELETE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);
            
        } catch(\Exception $e){
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_403'), 
                                        config('errors.custom_error_code.ERROR_20028'), 
                                        config('errors.custom_error_message.20028'));

        }
    }
}
