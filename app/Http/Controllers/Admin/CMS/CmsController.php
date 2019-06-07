<?php

namespace App\Http\Controllers\Admin\Cms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\FooterPage;
use App\FooterPagesLanguage;
use App\Helpers\Helpers;
use Validator;
use DB;

class CmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try { 
            // Get data for parent table
            $footerPage = FooterPage::get()->toArray();            
            if (empty($footerPage)) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('api_success_messages.success_message.MESSAGE_NO_DATA_FOUND');
                return Helpers::response($apiStatus, $apiMessage);
            }
            $pageList = array();
            foreach ($footerPage as $value) { 
                // Get data from child table                   
                $footerPageLanguage = FooterPagesLanguage::where('page_id', $value['page_id'])->get();
                $footerPageList = array();
                foreach ($footerPageLanguage as $language) {
                    $footerPageList[] = array('page_id' => $language['page_id'],
                                        'language_id' => $language['language_id'],
                                        'title' => $language['title'],
                                        'section' => (@unserialize($language['description']) === false) ? $language['description'] : unserialize($language['description']),
                                        );
                }
                $pageList[] = array('slug'  => $value['slug'],
                                    'pages' => $footerPageList
                                    );
            }
            // Set response data
            $apiData = $pageList; 
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CMS_LIST_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage, $apiData);                  
        } catch(\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_500'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_500'), 
                                        trans('api_error_messages.custom_error_code.ERROR_40018'), 
                                        trans('api_error_messages.custom_error_message.40018'));           
        }
    }

    /**
     * Store a newly created footer page in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        // Connect master database to get language details
        Helpers::switchDatabaseConnection('mysql', $request);
        $languages = DB::table('language')->get();    
        
        // Connect tenant database
        Helpers::switchDatabaseConnection('tenant', $request);

        // Server side validataions
        $validator = Validator::make($request->toArray(), ["page_detail" => "required"]);
        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20018'),
                                        $validator->errors()->first());
        } 
        try {   
            $postData = $request->page_detail;
            $slugValidator = Validator::make($postData, ["slug" => "required"]);
            // If post parameter have missing slug parameter
            if ($slugValidator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20038'),
                                            trans('api_error_messages.custom_error_message.20038'));
            } 
			if (count($postData['translations']) == 0) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20030'),
                                            trans('api_error_messages.custom_error_message.20030'));
            }
			
			// Check if section is exist in post data
            foreach ($postData['translations'] as $value) {
                if(!array_key_exists("section", $value)) {
					return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
									trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
									trans('api_error_messages.custom_error_code.ERROR_20036'),
									trans('api_error_messages.custom_error_message.20036'));
				} 
            } 
             
            // Set data for create new record
            $page = array();
            $page['status'] = config('constants.ACTIVE');
            $page['slug'] = $postData['slug'];
            // Create new cms page
            $footer_page = FooterPage::create($page);
			foreach ($postData['translations'] as $value) {                    
                // Server side validataions
                $validator = Validator::make($value, ["lang" => "required" ,"title" => "required" ,"section" => "required" ]);
                // If translations have any missing parameter
                if ($validator->fails()) {
                    return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                                trans('api_error_messages.custom_error_code.ERROR_20018'),
                                                $validator->errors()->first());
                }    
                // Get language_id from language code
                $language = $languages->where('code', $value['lang'])->first();
                $footer_page_language_data = array(	'page_id' => $footer_page['page_id'], 
													'language_id' => $language->language_id, 
													'title' => $value['title'], 
													'description' => serialize($value['section']));
                $footer_page_language = FooterPagesLanguage::create($footer_page_language_data);
                unset($footer_page_language_data);
            }
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CMS_PAGE_ADD_SUCCESS');
            $apiData = ['page_id' => $footer_page['page_id']];
            return Helpers::response($apiStatus, $apiMessage, $apiData);
                       
        } catch (\Exception $e) {
        	// Any other error occured when trying to insert data into database for `footer_page`
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int  $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        // Connect master database to get language details
        Helpers::switchDatabaseConnection('mysql', $request);
        $languages = DB::table('language')->get();    
        
        // Connect tenant database
        Helpers::switchDatabaseConnection('tenant', $request);

        // Server side validataions
        $validator = Validator::make($request->toArray(), ["page_detail" => "required"]);
        
		// If post parameter have any missing parameter
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20018'),
                                        $validator->errors()->first());
        }  
        
		$footerPage = FooterPage::find($id);
        
		if (!$footerPage) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20032'),
                                        trans('api_error_messages.custom_error_message.20032'));
        }

        $postData = $request->page_detail;
        $page = array();
		
		$slugValidator = Validator::make($postData, ["slug" => "required"]);
		// If post parameter have missing slug parameter
		if ($slugValidator->fails()) {
			return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20038'),
                                            trans('api_error_messages.custom_error_message.20038'));
		}
		
        $page['slug'] = $postData['slug'];
        $footer_page = FooterPage::where('page_id', $id)->update($page);

		if (count($postData['translations']) == 0) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
										trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
										trans('api_error_messages.custom_error_code.ERROR_20030'),
										trans('api_error_messages.custom_error_message.20030'));
        } 
        
		try {            
            foreach ($postData['translations'] as $value) {                    
                // Server side validataions
                $validator = Validator::make($value, ["lang" => "required" ,"title" => "required" ,"section" => "required" ]);
                // If translations have any missing parameter
                if ($validator->fails()) {
                    return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                                trans('api_error_messages.custom_error_code.ERROR_20036'),
                                                trans('api_error_messages.custom_error_message.20036'));
                }  
                $language = $languages->where('code', $value['lang'])->first();  
                $footerPageData = FooterPagesLanguage::where('page_id', $id)
								->where('language_id', $language->language_id)
                                ->count();
				
				$cms = array('page_id' => $footerPage['page_id'], 
                            'language_id' => $language->language_id, 
                            'title' => $value['title'], 
                            'description' => serialize($value['section']));
                
				if (!empty($footerPageData))
                    $footerPageLanguage = FooterPagesLanguage::where('page_id', $id)
										->where('language_id', $language->language_id)
										->update($cms);
                else
					$footerPageLanguage = FooterPagesLanguage::create($cms);
                    
                unset($cms);                    
			}      
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CMS_PAGE_UPDATE_SUCCESS');
            $apiData = ['page_id' => $id];
            return Helpers::response($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {           
            // Any other error occured when trying to update data into database for `footer_page`.
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
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CMS_PAGE_DELETE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);            
        } catch(\Exception $e){
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_403'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('api_error_messages.custom_error_code.ERROR_20020'), 
                                        trans('api_error_messages.custom_error_message.20020'));
        }
    }

    /**
     * Handle error while update.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleError()
    {
        return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_400'), 
                                    trans('api_error_messages.status_type.HTTP_STATUS_TYPE_400'), 
                                    trans('api_error_messages.custom_error_code.ERROR_20034'), 
                                    trans('api_error_messages.custom_error_message.20034'));
    }
}