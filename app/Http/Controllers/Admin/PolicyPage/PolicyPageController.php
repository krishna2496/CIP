<?php
namespace App\Http\Controllers\Admin\PolicyPage;

use App\Http\Controllers\Controller;
use App\Repositories\PolicyPage\PolicyPageRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;
use InvalidArgumentException;
use Illuminate\Validation\Rule;

//!  Policy Page Controller
/*!
  This controller is responsible for handling policy pages, store, update and delete operation including a trait.
*/
class PolicyPageController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\PolicyPage\PolicyPageRepository
     */
    private $policyPageRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\PolicyPage\PolicyPageRepository $policyPageRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(PolicyPageRepository $policyPageRepository, ResponseHelper $responseHelper)
    {
        $this->policyPageRepository = $policyPageRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display listing of policy pages
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $policyPages = $this->policyPageRepository->getPolicyPageList($request);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_POLICY_PAGE_LISTING');
            $apiMessage = ($policyPages->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_POLICY_PAGE_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $policyPages);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Store a newly created policy page in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "page_details" => "required",
                    "page_details.slug" => "required|max:255|unique:policy_page,slug,NULL,page_id,deleted_at,NULL",
                    "page_details.translations" => "required",
                    "page_details.translations.*.lang" => "required|max:2",
                    "page_details.translations.*.title" => "required",
                    "page_details.translations.*.sections" => "required",
                ]
            );


            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_POLICY_PAGE_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            // Create a new record
            $policyPage = $this->policyPageRepository->store($request);
            
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_POLICY_PAGE_CREATED');
            $apiData = ['page_id' => $policyPage['page_id']];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Get data for parent table
            $mission = $this->policyPageRepository->find($id);
            
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_PAGE_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $mission->toArray());
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_NO_DATA_FOUND'),
                trans('messages.custom_error_message.ERROR_NO_DATA_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->page_details,
                [
                    "slug" => [
                        "sometimes",
                        "required",
                        "max:255",
                         Rule::unique('policy_page')->ignore($id, 'page_id,deleted_at,NULL')],
                       "translations.*.lang" => "required_with:translations|max:2",
                       "translations.*.title" => "required_with:translations",
                       "translations.*.sections" => "required_with:translations"
                ]
            );
            
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_POLICY_PAGE_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }
            
            $policyPage = $this->policyPageRepository->update($request, $id);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_POLICY_PAGE_UPDATED');
            $apiData = ['page_id' => $id];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_POLICY_PAGE_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_POLICY_PAGE_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $policyPage = $this->policyPageRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_POLICY_PAGE_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_POLICY_PAGE_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_POLICY_PAGE_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
