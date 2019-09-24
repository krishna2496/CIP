<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use App\Repositories\TenantLanguage\TenantLanguageRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Validator;

class TenantLanguageController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\TenantLanguage\TenantLanguageRepository
     */
    private $tenantLanguageRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new tenant language controller instance.
     *
     * @param  App\Repositories\TenantLanguage\TenantLanguageRepository $tenantLanguageRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TenantLanguageRepository $tenantLanguageRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantLanguageRepository = $tenantLanguageRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display listing of tenant language.
     *
     * @param Illuminate\Http\Request $request
     * @param int $tenantId
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request, int $tenantId): JsonResponse
    {
        try {
            $tenantLanguageLists = $this->tenantLanguageRepository->getTenantLanguageList($request, $tenantId);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiData = $tenantLanguageLists;
            $apiMessage = (count($apiData) > 0)  ?
            trans('messages.success.MESSAGE_TENANT_LANGUAGE_LISTING') :
            trans('messages.custom_error_message.ERROR_TENANT_LANGUAGE_NOT_FOUND');
            
            return $this->responseHelper->successWithPagination($apiData, $apiStatus, $apiMessage);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        }
    }

    /**
     * Store/Update a newly created tenant language into database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->toArray(),
            [
                'tenant_id' => 'required|exists:tenant,tenant_id,deleted_at,NULL',
                'language_id'  => 'required|exists:language,language_id,deleted_at,NULL',
                'default'  => 'required|in:1,0'
            ]
        );

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }

        // Check for active status of language
        $languageStatus = $this->tenantLanguageRepository->checkLanguageStatus(
            $request->language_id,
            config('constants.language_status.ACTIVE')
        );
        if ($languageStatus->isEmpty()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_LANGUAGE_NOT_ACTIVE'),
                trans('messages.custom_error_message.ERROR_LANGUAGE_NOT_ACTIVE')
            );
        }

        // Store or update tenant language details
        $tenantLanguageData = $this->tenantLanguageRepository->storeOrUpdate($request->toArray());

        // Set response data
        $apiStatus = ($tenantLanguageData->wasRecentlyCreated) ? Response::HTTP_CREATED : Response::HTTP_OK;
        $apiMessage = ($tenantLanguageData->wasRecentlyCreated)
        ? trans('messages.success.MESSAGE_TENANT_LANGUAGE_ADDED')
        : trans('messages.success.MESSAGE_TENANT_LANGUAGE_UPDATED');
        $apiData = ['tenant_language_id' => $tenantLanguageData->tenant_language_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Remove tenant language details from storage.
     *
     * @param int $tenantLanguageId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $tenantLanguageId): JsonResponse
    {
        try {
            $tenantLanguageStatus = $this->tenantLanguageRepository->delete($tenantLanguageId);

            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_LANGUAGE_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_LANGUAGE_NOT_FOUND')
            );
        }
    }
}
