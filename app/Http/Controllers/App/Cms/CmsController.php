<?php

namespace App\Http\Controllers\App\Cms;

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
     * Display a listing of CMS pages.
     *
     * @return mixed
     */
    public function index()
    {
        try { 
            // Get data for parent table
            $footerPage = FooterPage::get()->toArray();            
            if (empty($footerPage)) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = config('messages.success_message.MESSAGE_NO_DATA_FOUND');
                return Helpers::response($apiStatus, $apiMessage);
            }
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
            }
            // Set response data
            $apiData = $footerPageList; 
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_CMS_LIST_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage, $apiData);                  
        } catch(\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_500'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_500'), 
                                        config('errors.custom_error_code.ERROR_40018'), 
                                        config('errors.custom_error_message.40018'));           
        }
    }  
}