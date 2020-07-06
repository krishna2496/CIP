<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;

class TenantHasSettings
{
    /**
     * @var App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     */
    private $tenantActivatedSettingRepository;

     /**
     * Create a new Tenant has setting instance
     *
     * @param App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     * @return void
     */
    public function __construct(
        TenantActivatedSettingRepository $tenantActivatedSettingRepository,
        ResponseHelper $responseHelper
    ) {
        $this->tenantActivatedSettingRepository = $tenantActivatedSettingRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$settings)
    {
        // Pre-Middleware Action
        foreach ($settings as $key => $setting) {
            $result = $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
                $setting,
                $request
            );
            if(!$result){
                return $this->responseHelper->error(
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    '',
                    trans('messages.custom_error_message.ERROR_UNAUTHORIZED'));
            }
        }
        $response = $next($request);
        return $response;
    }
}
