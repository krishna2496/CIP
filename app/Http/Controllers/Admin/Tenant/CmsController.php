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
     * Store a newly created footer page in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
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
                    // To delete data which are inserted before in parent table and child table
                    $footerPage = FooterPage::find($data['page_id']);
                    $footerPage->delete();
                    $footerPage->pageLanguages()->delete();                    
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
            $apiMessage = config('messages.success_message.MESSAGE_CMS_PAGE_ADD_SUCCESS');
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
     * @return mixed
     */
    public function update(Request $request, $id)
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
        $footerPage = FooterPage::findorFail($id);
        if (!empty($footerPage)) {
            try {
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
                    $footerPageData = FooterPagesLanguage::where('page_id', $id)
                                    ->where('language_id', $value['language_id'])
                                    ->count();
                    if (!empty($footerPageData)) {
                        // Update existing record 
                        $updatePage = array();
                        $updatePage['title'] = $value['title'];
                        $updatePage['description'] = $value['description'];
                        // Create footer language pages
                        $footerPageLanguage = FooterPagesLanguage::where('page_id', $id)->where('language_id', $value['language_id'])->update($updatePage);
                        unset($updatePage);                            
                    } else {
                        // Insert new record
                        $insertPage = array();
                        $insertPage['page_id'] = $id;
                        $insertPage['language_id'] = $value['language_id'];
                        $insertPage['title'] = $value['title'];
                        $insertPage['description'] = $value['description'];
                        // Create footer language pages
                        $footerPageLanguage = FooterPagesLanguage::create($insertPage);
                        unset($insertPage);                        
                    }                    
                }         
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = config('messages.success_message.MESSAGE_CMS_PAGE_UPDATE_SUCCESS');
                $apiData = ['page_id' => $id];
                return Helpers::response($apiStatus, $apiMessage, $apiData);
            } catch (\Exception $e) {
               // Any other error occured when trying to insert data into database for tenant option.
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                        config('errors.custom_error_code.ERROR_20004'), 
                                        config('errors.custom_error_message.20004'));
                
            }
        } else {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20022'),
                                        config('errors.custom_error_message.20022'));
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
            $footerPage = FooterPage::find($id);
            $footerPage->delete();
            $footerPage->pageLanguages()->delete();

            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();            
            $apiMessage = config('messages.success_message.MESSAGE_CMS_PAGE_DELETE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);            
        } catch(\Exception $e){
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_403'), 
                                        config('errors.custom_error_code.ERROR_20020'), 
                                        config('errors.custom_error_message.20020'));
        }
    }
}