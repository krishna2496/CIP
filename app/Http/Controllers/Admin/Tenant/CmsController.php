<?php

namespace App\Http\Controllers\Admin\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\FooterPage;
use App\FooterPagesLanguage;
use App\Helpers\Helpers;
use Validator;

class CmsController extends Controller
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
        $validator = Validator::make($request->toArray(), ["page_detail" => "required"]);

        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20018'),
                                        $validator->errors()->first());
        }    

        try {
            // Set data for create new record
            $insert = array();
            $insert['status'] = 1;
         
            // Create new cms page
            $data = FooterPage::create($insert);
            $pageData = $request->page_detail;

            foreach ($pageData['translations'] as $value) {
                
                // Server side validataions
                $validator = Validator::make($value, ["language_id" => "required" ,"title" => "required" ,"description" => "required" ]);

                // If translations have any missing parameter
                if ($validator->fails()) {
                    return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                                config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                                config('errors.custom_error_code.ERROR_20018'),
                                                $validator->errors()->first());
                }    

                $insertPage = array();
                $insertPage['page_id'] = $data['page_id'];
                $insertPage['language_id'] = $value['language_id'];
                $insertPage['title'] = $value['title'];
                $insertPage['description'] = $value['description'];

                // Create footer language pages
                $footerPageLanguage = FooterPagesLanguage::create($insertPage);
                unset($insertPage);
            }

            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_FOOTER_PAGE_ADD_SUCCESS');
            $apiData = ['page_id' => $data['page_id']];
            return Helpers::response($apiStatus, $apiMessage, $apiData);

        } catch (\Exception $e) {
            // Any other error occured when trying to insert data into database for tenant option.
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
