<?php

namespace App\Http\Controllers\App\Tenant;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TenantCurrencyController extends Controller
{
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        Helpers $helpers,
        ResponseHelper $responseHelper
    ) {
        $this->helpers = $helpers;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Fetch all tenant currency
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        // Fetch tenant all currency details
        $getTenantCurrency = $this->helpers->getTenantCurrency($request);

        // Set response data
        $apiData = $getTenantCurrency->toArray();
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($getTenantCurrency->isEmpty() || $getTenantCurrency->isEmpty())
        ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
        trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
