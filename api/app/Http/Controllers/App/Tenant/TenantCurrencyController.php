<?php

namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\Helpers;

class TenantCurrencyController extends Controller
{
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        Helpers $helpers
    ) {
        $this->helpers = $helpers;
    }

    /**
     * Fetch all tenant currency
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse{

        // Fetch tenant all settings details
        $getTenantCurrency = $this->helpers->getTenantCurrency($request);

        dd($getTenantCurrency);

        $apiData = $getTenantCurrency;

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($getTenantCurrency->isEmpty() || $getTenantCurrency->isEmpty())
        ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
        trans('messages.success.MESSAGE_TENANT_CURRENCY_LISTING');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
