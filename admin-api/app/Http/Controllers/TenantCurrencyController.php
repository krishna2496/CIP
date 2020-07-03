<?php

namespace App\Http\Controllers;

use App\Repositories\Currency\CurrencyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\TenantCurrency;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\DatabaseHelper;
use DB;
use App\Events\ActivityLogEvent;
use Validator;

//!  TenantCurrencyController controller
/*!
This controller is responsible for handling currency setting store/delete and show operations.
 */
class TenantCurrencyController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Currency\CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\DatabaseHelper
     */
    private $databaseHelper;

    /**
     * Create a new Tenant has setting controller instance.
     *
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\DatabaseHelper $databaseHelper
     * @param App\Repositories\Currency\CurrencyRepository $currencyRepository
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        DatabaseHelper $databaseHelper,
        CurrencyRepository $currencyRepository
    ) {
        $this->responseHelper = $responseHelper;
        $this->databaseHelper = $databaseHelper;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * Show tenant Setting details
     *
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function index(Request $request, int $tenantId): JsonResponse
    {
        try {
            $tenantCurrencyList = $this->currencyRepository->getCurrencyDetails($request, $tenantId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiData = $tenantCurrencyList;
            $apiMessage = (count($apiData) > 0)  ?
                trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING') :
                trans('messages.custom_error_message.ERROR_TENANT_CURRENCY_NOT_FOUND');
                        
            return $this->responseHelper->successWithPagination($apiData, $apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_NOT_FOUND')
            );
        }
    }

    /**
     * Store a newly created tenant settings into database
     *
     * @param \Illuminate\Http\Request $request
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'currency' => 'required',
            'currency.*.code' => 'required|regex:/^[A-Z]{3}$/',
            'currency.*.tenant_id' => 'required|exists:tenant,tenant_id,deleted_at,NULL',
            'settings.*.default' => 'in:0,1',
            'settings.*.is_active' => 'in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_CURRENCY_FIELD_REQUIRED'),
                $validator->errors()->first()
            );
        }

        if (!$this->currencyRepository->checkAvailableLanguage($request)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_CURRENCY_CODE_NOT_AVAILABLE'),
                trans('messages.custom_error_message.ERROR_CURRENCY_CODE_NOT_AVAILABLE')
            );
        }

        $this->currencyRepository->storeOrUpdate($request->toArray());

        // Store or update tenant currency details
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_TENANT_CURRENCY_ADDED');

        // Make activity log
        event(new ActivityLogEvent(
            config('constants.activity_log_types.TENANT_CURRENCY'),
            config('constants.activity_log_actions.CREATED'),
            get_class($this),
            $request->toArray()
        ));

        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
