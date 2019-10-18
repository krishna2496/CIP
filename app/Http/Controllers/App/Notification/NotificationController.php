<?php

namespace App\Http\Controllers\App\Notification;

use App\Repositories\NotificationType\NotificationTypeRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Validator;
use App\Services\NotificationService;
use App\Helpers\Helpers;
use Carbon\Carbon;

class NotificationController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\NotificationType\NotificationTypeRepository
     */
    private $notificationTypeRepository;

    /**
     * @var App\Repositories\Notification\NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Service\NotificationService
     */
    private $notificationService;

    /**
     * @var App\Helpers\Helpers
     */
    public $helpers;

    /**
     * Create a new notification type controller instance.
     *
     * @param App\Repositories\NotificationType\NotificationTypeRepository $notificationTypeRepository
     * @param App\Repositories\Notification\NotificationRepository $notificationRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Service\NotificationService $notificationService
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        NotificationTypeRepository $notificationTypeRepository,
        NotificationRepository $notificationRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        NotificationService $notificationService,
        Helpers $helpers
    ) {
        $this->notificationTypeRepository = $notificationTypeRepository;
        $this->notificationRepository = $notificationRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->notificationService = $notificationService;
        $this->helpers = $helpers;
    }

    /**
     * Fetch notification settings.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $languageId = $this->languageHelper->getLanguageId($request);
        $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);

        //Fetch unread notification count
        $notificationsCount = $this->notificationRepository->getNotificationsCount($request->auth->user_id);
        $notificationData['unread_notifications'] = $notificationsCount;

        //Fetch notification
        $notifications = $this->notificationRepository->getNotifications($request->auth->user_id);
        
        foreach ($notifications as $notification) {
            $notificaionType = str_replace("_", " ", $notification->notificationType->notification_type);
            $methodName =  lcfirst(str_replace(" ", "", ucwords($notificaionType)));
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $notificationDetails = $this->notificationService->$methodName(
                $notification,
                $tenantName,
                $languageId,
                $defaultTenantLanguage->language_id
            );
            $notificationDetails['created_at'] = Carbon::parse($notification->created_at)->format('Y-m-d H:i:s');
            $notificationData['notifications'][] = $notificationDetails;
        }

        // Set response data
        $apiData = $notificationData;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (count($notifications) > 0) ? trans('messages.success.MESSAGE_NOTIFICATION_LISTING') :
        trans('messages.success.MESSAGE_NO_RECORD_FOUND');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
