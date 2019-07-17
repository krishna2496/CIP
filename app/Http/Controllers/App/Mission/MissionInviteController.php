<?php
namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionInvite\MissionInviteRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use App\Repositories\User\UserRepository;
use App\Repositories\Mission\MissionRepository;
use Illuminate\Support\Facades\Mail;
use App\Jobs\AppMailerJob;

class MissionInviteController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var MissionInviteRepository
     */
    private $missionInviteRepository;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /*
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new Mission controller instance.
     *
     * @param App\Repositories\Mission\MissionInviteRepository $missionInviteRepository
     * @param App\Repositories\Notification\NotificationRepository $notificationRepository
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param  Illuminate\Http\LanguageHelper $languageHelper
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        MissionInviteRepository $missionInviteRepository,
        NotificationRepository $notificationRepository,
        UserRepository $userRepository,
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers
    ) {
        $this->missionInviteRepository = $missionInviteRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
    }

    /*
     * Invite to a mission
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function missionInvite(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    "mission_id" => "numeric|required|exists:mission,mission_id",
                    "to_user_id" => "numeric|required|exists:user,user_id",
                ]
            );
    
            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_INVALID_INVITE_MISSION_DATA'),
                    $validator->errors()->first()
                );
            }
            // Check if user is already invited for this mission
            $getMissionInvite = $this->missionInviteRepository->getInviteMission(
                $request->mission_id,
                $request->to_user_id,
                $request->auth->user_id
            );
            if (!$getMissionInvite->isEmpty()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_INVITE_MISSION_ALREADY_EXIST'),
                    trans('messages.custom_error_message.ERROR_INVITE_MISSION_ALREADY_EXIST')
                );
            }
            $inviteMission = $this->missionInviteRepository->inviteMission(
                $request->mission_id,
                $request->to_user_id,
                $request->auth->user_id
            );
            
            $notificationTypeId = $this->notificationRepository
            ->getNotificationTypeID(config('constants.notification_types.RECOMMENDED_MISSIONS'));
            
            // Check if to_user_id (colleague) has enabled notification for Recommended missions
            $notifyColleague = $this->notificationRepository
            ->userNotificationSetting($request->to_user_id, $notificationTypeId);
                
            if ($notifyColleague) {
                $colleague = $this->userRepository->find($request->to_user_id);
                $colleagueEmail = $colleague->email;
                $colleagueLanguageId = $colleague->language_id;
                $languages = $this->languageHelper->getLanguages($request);
                $language = $languages->where('language_id', $colleagueLanguageId)->first();
                $colleagueLanguage = $language->code;
                $fromUserName = $this->userRepository->getUserName($request->auth->user_id);
                $missionName = $this->missionRepository->getMissionName(
                    $request->mission_id,
                    $colleague->language_id
                );
                $notificationData = array(
                    'notification_type_id' => $notificationTypeId,
                    'user_id' => $request->auth->user_id,
                    'to_user_id' => $request->to_user_id,
                    'mission_id' => $request->mission_id,
                );
                $notification = $this->notificationRepository->createNotification($notificationData);
                
                $data = array(
                    'missionName'=> $missionName,
                    'fromUserName'=> $fromUserName,
                    'colleagueLanguage'=> $colleagueLanguage
                );

                try {
                    $tenantName = $this->helpers->getSubDomainFromRequest($request);
                } catch (\Exception $e) {
                    return $this->badRequest($e->getMessage());
                }
                            
                try {
                    $params['tenant_name'] = $tenantName;
                    $params['to'] = $colleagueEmail; //required
                    $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TEMPLATE_USER_INVITE'); //path to the email template
                    $params['subject'] = trans('mail.recommonded_mission.MAIL_MISSION_RECOMMENDATION', [], $colleagueLanguage); //optional
                    $params['data'] = $data;

                    dispatch(new AppMailerJob($params));
                } catch (\Exception $e) {
                    return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
                }
            }
            
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_INVITED_FOR_MISSION');
            $apiData = ['mission_invite_id' => $inviteMission->mission_invite_id];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
