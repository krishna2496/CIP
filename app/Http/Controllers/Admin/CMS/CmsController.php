<?php

namespace App\Http\Controllers\Admin\CMS;

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
            $pageData = $request->page_detail;
            if (count($pageData['translations']) == 0) {
                return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                                config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                                config('errors.custom_error_code.ERROR_20030'),
                                                config('errors.custom_error_message.20030'));
            } 
            // Set data for create new record
            $insert = array();
            $insert['status'] = config('constants.ACTIVE');
            // Create new cms page
            $data = FooterPage::create($insert);
            foreach ($pageData['translations'] as $value) {                    
                // Server side validataions
                $validator = Validator::make($value, ["language_id" => "required" ,"title" => "required" ,"section" => "required" ]);
                // If translations have any missing parameter
                if ($validator->fails()) {
                    return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                                config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                                config('errors.custom_error_code.ERROR_20018'),
                                                $validator->errors()->first());
                }    
                $cms = array('page_id' => $data['page_id'], 'language_id' => $value['language_id'], 'title' => $value['title'], 'description' => serialize($value['section']));
                // Create footer language pages
                $footerPageLanguage = FooterPagesLanguage::create($cms);
                unset($cms);
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
        $footerPage = FooterPage::find($id);
        if (!$footerPage) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                        config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                        config('errors.custom_error_code.ERROR_20032'),
                                        config('errors.custom_error_message.20032'));
        }
        $pageData = $request->page_detail;
        if (count($pageData['translations']) == 0) {
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'),
                                            config('errors.status_type.HTTP_STATUS_TYPE_422'),
                                            config('errors.custom_error_code.ERROR_20030'),
                                            config('errors.custom_error_message.20030'));
        } 
        try {            
            foreach ($pageData['translations'] as $value) {                    
                // Server side validataions
                $validator = Validator::make($value, ["language_id" => "required" ,"title" => "required" ,"section" => "required" ]);
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
                    $cms = array('page_id' => $footerPage['page_id'], 'language_id' => $value['language_id'], 'title' => $value['title'], 'description' => serialize($value['section']));

                    // Create footer language pages
                    $footerPageLanguage = FooterPagesLanguage::where('page_id', $id)->where('language_id', $value['language_id'])->update($cms);
                    unset($cms);                            
                } else {
                    // Insert new record
                    $cms = array('page_id' => $footerPage['page_id'], 'language_id' => $value['language_id'], 'title' => $value['title'], 'description' => serialize($value['section']));
                    // Create footer language pages
                    $footerPageLanguage = FooterPagesLanguage::create($cms);
                    unset($cms);                        
                }                    
            }         
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_CMS_PAGE_UPDATE_SUCCESS');
            $apiData = ['page_id' => $id];
            return Helpers::response($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            // Any other error occured when trying to update data into database for tenant option.
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_422'), 
                                    config('errors.status_type.HTTP_STATUS_TYPE_422'), 
                                    config('errors.custom_error_code.ERROR_20004'), 
                                    config('errors.custom_error_message.20004'));
            
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
            $footerPage = FooterPage::findOrFail($id);
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

    /**
     * Handle error while update.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function handleError()
    {
        return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
                                    config('errors.status_type.HTTP_STATUS_TYPE_403'), 
                                    config('errors.custom_error_code.ERROR_20034'), 
                                    config('errors.custom_error_message.20034'));
    }
}