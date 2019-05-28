<?php

namespace App\Http\Controllers\Admin\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiResponseController;
use Illuminate\Support\Facades\Input;
use App\TenantOption;
use Validator;

class TenantOptionsController extends ApiResponseController
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSlider(Request $request)
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), [
            "slider_image" => "required|mimes:png,jpg,jpeg",
            "slider_detail" => "required",
            "sort_order" => "required"
        ]);

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20016'),
                                        $validator->errors()->first());
        }        

        try {

            // Get total count of "Login_screen_slider"
            $tenantOptions = TenantOption::get(['option_name', 'option_value'])->where('option_name', 'Login_screen_slider');

            // Prevent data insertion if user is trying to insert more than 4 records 
            if(count($tenantOptions) >= 4){
                // Set response data
                $this->apiStatus = app('Illuminate\Http\Response')->status();
                $this->apiMessage = "Sorry, you cannot add more than 4 sliders!";
            }
            else{
                
                // Check file is available or not
                if ($request->hasFile('slider_image')) {
                    // Check file is valid or not
                    $file = $request->file('slider_image');
                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = "slider_".time().".".$extension;
                        $destinationPath = "images/";

                        // Upload file on destination path
                        $uploadedFile = $file->move($destinationPath, $fileName);
                    }
                }
                // Set data for option_value
                $sliderDetails = json_decode($request->slider_detail);
                $optionValue = array();
                $optionValue['url'] = $fileName;
                $optionValue['sort_order'] = $request->sort_order;
                $optionValue['translations'] = $sliderDetails->translations;

                // Set data for create new record
                $insert = array();
                $insert['option_name'] = 'Login_screen_slider';
                $insert['option_value'] = json_encode($optionValue);

                // Create new tenant_option
                $tenantOption = TenantOption::create($insert);

                // Set response data
                $this->apiStatus = app('Illuminate\Http\Response')->status();
                $this->apiMessage = "Slider added successfully";
            }
            return $this->response();

        } catch (\Exception $e) {

            // Any other error occured when trying to insert data into database for tenant option.
            return $this->errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                    config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                    config('errors.custom_error_code.ERROR_20004'), 
                                    config('errors.custom_error_message.20004'));
            
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
