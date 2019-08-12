<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Repositories\TenantHasOption\TenantHasOptionRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use App\Repositories\Tenant\TenantRepository;

class TenantHasOptionController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Tenant\TenantHasOptionRepository
     */
    private $tenantHasOptionRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Tenant has option controller instance.
     *
     * @param  App\Repositories\TenantHasOption\TenantHasOptionRepository $tenantHasOptionRepository
     * @param  App\Repositories\Tenant\TenantRepository $tenantRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        TenantHasOptionRepository $tenantHasOptionRepository,
        TenantRepository $tenantRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantHasOptionRepository = $tenantHasOptionRepository;
        $this->tenantRepository = $tenantRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Show tenant option details
     *
     * @param int $tenantId
     * @return \Illuminate\Http\JsonResponse;
     */
    public function show(int $tenantId): JsonResponse
    {
        try {
            // Find tenant
            $tenant = $this->tenantRepository->find($tenantId);
            
            // Set response message
            $apiStatus = Response::HTTP_OK;
            $apiData = $tenant->options->toArray();
            $apiMessage = (!empty($apiData)) ? trans('messages.success.MESSAGE_TENANT_OPTION_LISTING') :
            trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_TENANT_NOT_FOUND'))
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.999999'));
        }
    }
}
