<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthCheckController extends Controller
{
    public function __invoke() : JsonResponse
    {
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_TENANT_BACKGROUND_PROCESS_COMPLETED');
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
