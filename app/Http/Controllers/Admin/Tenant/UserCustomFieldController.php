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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
            // Get data for options
            $translation = $request->translation;
            if ((($request->type == 'Drop-down' ) || ($request->type == 'radio')) && ($translation['values'] == "")) {
                // Set response data if values are null for Drop-down and radio
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                    config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                    config('errors.custom_error_code.ERROR_20026'),
                                    config('errors.custom_error_message.20026'));
            } else {                    
                // Set data for create new record
                $insert = array();
                $insert['name'] = $request->name;
                $insert['type'] = $request->type;
                $insert['is_mandatory'] = $request->is_mandatory;
                $insert['translations'] = json_encode($translation);
                // Create new tenant_option
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
