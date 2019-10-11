<?php

namespace App\Http\Controllers\App\Notification;

use App\Repositories\NotificationType\NotificationTypeRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class NotificationTypeController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\NotificationType\NotificationTypeRepository
     */
    private $notificationTypeRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new notification type controller instance.
     *
     * @param App\Repositories\NotificationType\NotificationTypeRepository $notificationTypeRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        NotificationTypeRepository $notificationTypeRepository,
        ResponseHelper $responseHelper
    ) {
        $this->notificationTypeRepository = $notificationTypeRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Fetch notification settings.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        //Fetch notification settings
        $notificationSettings = $this->notificationTypeRepository->getNotificationSettings($request->auth->user_id);

        // Set response data
        $apiData = $notificationSettings->toArray();
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($notificationSettings->isEmpty()) ?
        trans('messages.success.MESSAGE_NO_RECORD_FOUND'):
        trans('messages.success.MESSAGE_NOTIFICATION_SETTINGS_LISTING');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
