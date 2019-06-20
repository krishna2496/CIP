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
                $apiMessage = trans('api_success_messages.success_message.MESSAGE_NO_DATA_FOUND');
                return Helpers::response($apiStatus, $apiMessage);
            }
            $pageList = array();
            foreach ($footerPage as $value) {
                // Get data from child table
                $footerPageList = array();
                $footerPageLanguage = FooterPagesLanguage::where('page_id', $value['page_id'])->get();
                foreach ($footerPageLanguage as $language) {
                    $footerPageList[] = array('page_id' => $language['page_id'],
                                        'language_id' => $language['language_id'],
                                        'title' => $language['title']
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
        } catch (\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(
                trans('api_error_messages.status_code.HTTP_STATUS_500'),
                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_500'),
                trans('api_error_messages.custom_error_code.ERROR_40018'),
                trans('api_error_messages.custom_error_message.40018')
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return mixed
     */
    public function show($slug)
    {
        try {
            // Get data for parent table
            $footerPage = FooterPage::where('slug', $slug)->first();

            if (empty($footerPage)) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('api_success_messages.success_message.MESSAGE_NO_DATA_FOUND');
                return Helpers::response($apiStatus, $apiMessage);
            }
            // Get data from child table
            $footerPageLanguage = FooterPagesLanguage::where('page_id', $footerPage['page_id'])->get();
            $footerPageList = array();
            foreach ($footerPageLanguage as $language) {
                $footerPageList[] = array('page_id' => $language['page_id'],
                                    'language_id' => $language['language_id'],
                                    'title' => $language['title'],
                                    'section' => (@unserialize($language['description']) === false) ? $language['description'] : unserialize($language['description']),
                                    );
            }
            $pageList = array('slug'  => $footerPage['slug'],
                              'pages' => $footerPageList
                            );
            // Set response data
            $apiData = $pageList;
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_CMS_LIST_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(
                trans('api_error_messages.status_code.HTTP_STATUS_500'),
                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_500'),
                trans('api_error_messages.custom_error_code.ERROR_40018'),
                trans('api_error_messages.custom_error_message.40018')
            );
        }
    }


    /**
     * Display a listing of CMS pages.
     *
     * @return mixed
     */
    public function cmsList()
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
        } catch (\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(
                trans('api_error_messages.status_code.HTTP_STATUS_500'),
                trans('api_error_messages.status_type.HTTP_STATUS_TYPE_500'),
                trans('api_error_messages.custom_error_code.ERROR_40018'),
                trans('api_error_messages.custom_error_message.40018')
            );
        }
    }
}
