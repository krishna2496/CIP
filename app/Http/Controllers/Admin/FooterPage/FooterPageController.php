<?php
namespace App\Http\Controllers\Admin\FooterPage;

use App\Http\Controllers\Controller;
use App\Repositories\FooterPage\FooterPageRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use Validator;
use DB;
use PDOException;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FooterPageController extends Controller
{
    /**
     * @var App\Repositories\FooterPage\FooterPageRepository
     */
    private $footerPageRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\FooterPage\FooterPageRepository $footerPageRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(FooterPageRepository $footerPageRepository, ResponseHelper $responseHelper)
    {
        $this->footerPageRepository = $footerPageRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $footerPages = $this->footerPageRepository->footerPageList($request);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            $apiMessage = ($footerPages->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_FOOTER_PAGE_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $footerPages);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Store a newly created footer page in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "page_details" => "required",
                    "page_details.slug" => "required",
                    "page_details.translations" => "required",
                    "page_details.translations.*.lang" => "required",
                    "page_details.translations.*.title" => "required",
                    "page_details.translations.*.sections" => "required",
                ]
            );


            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts['422'],
                    trans('messages.custom_error_code.ERROR_300000'),
                    $validator->errors()->first()
                );
            }
            
            $postData = $request->page_details;
            
            $footerPage = $this->footerPageRepository->store($request);
            
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_CREATED');
            $apiData = ['page_id' => $footerPage['page_id']];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
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
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                "page_details" => "required",
                "page_details.translations.*.lang" => "required_with:page_details.translations",
                "page_details.translations.*.title" => "required_with:page_details.translations",
                "page_details.translations.*.sections" => "required_with:page_details.translations",
                ]
            );
            
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_300000'),
                    $validator->errors()->first()
                );
            }
            
            $footerPage = $this->footerPageRepository->update($request, $id);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_UPDATED');
            $apiData = ['page_id' => $id];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $footerPage = $this->footerPageRepository->delete($id);
            
            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_STATUS_NO_CONTENT');
            $apiMessage = trans('messages.success.MESSAGE_FOOTER_PAGE_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.300005'));
        }
    }
}
