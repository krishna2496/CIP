<?php

namespace App\Http\Controllers\App\Notification;

use App\Repositories\NotificationType\NotificationTypeRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Validator;

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
        $apiMessage = trans('messages.success.MESSAGE_NOTIFICATION_SETTINGS_LISTING');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
      
    /**
     * Store or update user notification settings into database
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'settings' => 'required',
            'settings.*.notification_type_id' =>
            'required|exists:notification_type,notification_type_id,deleted_at,NULL',
            'settings.*.value' => 'required|in:0,1',
            ]);

        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_USER_NOTIFICATION_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        
        // Store or update user notification settings
        $this->notificationTypeRepository->storeOrUpdateUserNotification($request->toArray(), $request->auth->user_id);
        
        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage =  trans('messages.success.MESSAGE_USER_NOTIFICATION_SETTINGS_UPDATED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
