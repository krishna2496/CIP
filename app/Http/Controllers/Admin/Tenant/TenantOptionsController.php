<?php

namespace App\Http\Controllers\Admin\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\TenantOption;
use App\Helpers\Helpers;
use Validator;

class TenantOptionsController extends Controller
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
     * Store slider details.
     *
     * @param \Illuminate\Http\Request  $request
     * @return mixed response
     */
    public function storeSlider(Request $request)
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), ["slider_image" => "required|mimes:png,jpg,jpeg"
        ]);

        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20018'),
                                        $validator->errors()->first());
        }        

        try {
            // Get total count of "slider"
            $slider = TenantOption::get(['option_name', 'option_value'])->where('option_name', config('constants.TENANT_OPTION_SLIDER'));

            // Prevent data insertion if user is trying to insert more than defined slider limit records 
            if (count($slider) >= config('constants.SLIDER_LIMIT')) {
                // Set response data
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_403'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('api_error_messages.custom_error_code.ERROR_40020'), 
                                        trans('api_error_messages.custom_error_message.40020'));
            } else {                
                // Check file is available or not
                if ($request->hasFile('slider_image')) {
                    // Check file is valid or not
                    $file = $request->file('slider_image');
                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = "slider_".time().".".$extension;
                        $destinationPath = config('constants.SLIDER_IMAGE_PATH');

                        // Upload file on destination path
                        $uploadedFile = $file->move($destinationPath, $fileName);
                    }
                }
                // Set data for option_value
                $sliderDetails = (isset($request->slider_detail)) ? json_decode($request->slider_detail) : "";
                $optionValue = array();
                $optionValue['url'] = $fileName;
                $optionValue['sort_order'] = (isset($request->sort_order)) ? $request->sort_order : 0;
                $optionValue['translations'] = ($sliderDetails != '') ? $sliderDetails->translations : "";

                // Set data for create new record
                $insert = array();
                $insert['option_name'] = config('constants.TENANT_OPTION_SLIDER');
                $insert['option_value'] = serialize(json_encode($optionValue));

                // Create new tenant_option
                $tenantOption = TenantOption::create($insert);

                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('api_success_messages.success_message.MESSAGE_SLIDER_ADD_SUCCESS');
                return Helpers::response($apiStatus, $apiMessage);
            }

        } catch (\Exception $e) {
            // Any other error occured when trying to insert data into database for tenant option.
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'), 
                                    trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'), 
                                    trans('api_error_messages.custom_error_code.ERROR_20004'), 
                                    trans('api_error_messages.custom_error_message.20004'));
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
