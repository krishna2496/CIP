<?php
namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionInvite\MissionInviteRepository;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class MissionInviteController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var MissionInviteRepository
     */
    private $missionInviteRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Mission controller instance.
     *
     * @param App\Repositories\Mission\MissionInviteRepository $missionInviteRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionInviteRepository $missionInviteRepository,
        ResponseHelper $responseHelper
    ) {
        $this->missionInviteRepository = $missionInviteRepository;
        $this->responseHelper = $responseHelper;
    }

    /*
     * Invite to mission
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
                    "mission_id" => "numeric|required",
                    "to_user_id" => "numeric|required",
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
            $inviteCount = $this->missionInviteRepository->checkInviteMission(
                $request->mission_id,
                $request->to_user_id,
                $request->auth->user_id
            );
            if ($inviteCount > 0) {
                return $this->invalidArgument(
                    config('constants.error_codes.ERROR_INVITE_MISSION_ALREADY_EXIST'),
                    trans('messages.custom_error_message.ERROR_INVITE_MISSION_ALREADY_EXIST')
                );
            }
            $inviteMission = $this->missionInviteRepository->inviteMission(
                $request->mission_id,
                $request->to_user_id,
                $request->auth->user_id
            );
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
