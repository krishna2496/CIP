<?php
namespace App\Http\Controllers\Admin\FooterPage;

use App\Repositories\FooterPage\FooterPageRepository;
use Illuminate\Http\{Request, Response};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator, DB, PDOException;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
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
			$footerPages = $this->page->footerPageList($request);
			if (empty($footerPages)) {
                // Set response data
                $apiStatus = trans('messages.status_code.HTTP_STATUS_NOT_FOUND');
                $apiMessage = trans('messages.custom_error_message.MESSAGE_NO_RECORD_FOUND');
                return ResponseHelper::error($apiStatus, $apiMessage);
            }
			
			// Set response data
            $apiData = $footerPages;
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            return ResponseHelper::successWithPagination($apiStatus, $apiMessage, $apiData);                  
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
															"page_details.translations.*.lang" => "required_with:page_details.translations",
															"page_details.translations.*.title" => "required_with:page_details.translations",
															"page_details.translations.*.sections" => "required_with:page_details.translations",
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
