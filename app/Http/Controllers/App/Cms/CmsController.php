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
                $footerPageData = FooterPagesLanguage::where('page_id', $value['page_id'])->get();
                $detail = array();
                $detailArray = array();
                foreach ($footerPageData as $language) {
                    //flag to check value is serialize or not
                    $checkForSerialize = false;
                    //check if value is serialize or not
                    $checkForSerialize = @unserialize($language['description']);
                    if ($checkForSerialize === false) 
                        $description = $language['description'];
                    else 
                        $description = unserialize($language['description']);
                    
                    $detailArray['language_id'] = $language['language_id'];
                    $detailArray['title'] = $language['title'];
                    $detailArray['section'] = $description;
                    $detailArray['page_id'] = $language['page_id'];
                    $detail[] = $detailArray;
                }
                $footerPageList[] = $detail;
            }
            // Set response data
            $apiData = $footerPageList; 
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = config('messages.success_message.MESSAGE_CMS_LIST_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage, $apiData);                  
        } catch(\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(config('errors.status_code.HTTP_STATUS_403'), 
                                        config('errors.status_type.HTTP_STATUS_TYPE_403'), 
                                        config('errors.custom_error_code.ERROR_40018'), 
                                        config('errors.custom_error_message.40018'));           
        }
    }  
}