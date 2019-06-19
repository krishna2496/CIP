<?php
namespace App\Http\Controllers\Admin\FooterPage;

use App\Repositories\FooterPage\FooterPageRepository;
use Illuminate\Http\{Request, Response};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator, DB, PDOException;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FooterPageController extends Controller
{
	private $page;
	
	private $response;
	
	public function __construct(FooterPageRepository $page, Response $response)
    {
		 $this->page = $page;
		 $this->response = $response;
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try { 
			
			$footerPage = $this->page->footerPageList($request);
			print_R($footerPage);exit;
			if (empty($pageList)) {
                // Set response data
                $apiStatus = trans('messages.status_code.HTTP_STATUS_NOT_FOUND');
                $apiMessage = trans('messages.custom_error_message.MESSAGE_NO_RECORD_FOUND');
                return ResponseHelper::error($apiStatus, $apiMessage);
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
			dd($e);
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
		// return $this->page->store($request);
		try {
			// Server side validataions
			$validator = Validator::make($request->all(), ["page_details" => "required", 
															"page_details.slug" => "required",
															"page_details.translations" => "required",
															"page_details.translations.*.lang" => "required",
															"page_details.translations.*.title" => "required",
															"page_details.translations.*.sections" => "required",
															]);

			// If request parameter have any error
			if ($validator->fails()) {
				return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
											trans('messages.status_type.HTTP_STATUS_TYPE_422'),
											trans('messages.custom_error_code.ERROR_300000'),
											$validator->errors()->first());
			}
			
			$postData = $request->page_details;
			
			$footerPage = $this->page->store($request);
			
			// Set response data
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_CREATED');
            $apiData = ['page_id' => $footerPage['page_id']];
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
		 } catch(PDOException $e) {
			
			throw new PDOException($e->getMessage());
			
		} catch(\Exception $e) {
			
			throw new \Exception($e->getMessage());
			
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
        try {
			// Server side validataions
			$validator = Validator::make($request->all(), ["page_details" => "required", 
															"page_details.slug" => "required",
															"page_details.translations" => "required",
															"page_details.translations.*.lang" => "required",
															"page_details.translations.*.title" => "required",
															"page_details.translations.*.sections" => "required",
															]);
			
			// If post parameter have any missing parameter
			if ($validator->fails()) {
				return ResponseHelper::error(trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
												trans('messages.status_type.HTTP_STATUS_TYPE_422'),
												trans('messages.custom_error_code.ERROR_300000'),
												$validator->errors()->first());
			}
			
			$footerPage = $this->page->update($request, $id);

			// Set response data
			$apiStatus = $this->response->status();
			$apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_UPDATED');
			$apiData = ['page_id' => $id];
			return ResponseHelper::success($apiStatus, $apiMessage, $apiData);
			
		} catch (ModelNotFoundException $e) {
			throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
        } catch (PDOException $e) {
			throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
            $footerPage = $this->page->delete($id);
            
            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_STATUS_NO_CONTENT');
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_DELETED');
            return ResponseHelper::success($apiStatus, $apiMessage);            
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
        }
    }
}
