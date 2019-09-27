<?php
namespace App\Http\Controllers\App\Mission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionComment\MissionCommentRepository;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use App\Helpers\Helpers;
use App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository;

class MissionCommentController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var ResponseHelper
     */
    private $responseHelper;

    /**
     * @var MissionCommentRepository
     */
    private $missionCommentRepository;
    
    /**
     * @var App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     */
    private $tenantActivatedSettingRepository;
    
    /**
     * Create a new comment controller instance
     *
     * @param App\Repositories\Mission\MissionCommentRepository $missionCommentRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers
     * @param App\Repositories\TenantActivatedSetting\TenantActivatedSettingRepository
     * @return void
     */
    public function __construct(
        MissionCommentRepository $missionCommentRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers,
        TenantActivatedSettingRepository $tenantActivatedSettingRepository
    ) {
        $this->missionCommentRepository = $missionCommentRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
        $this->tenantActivatedSettingRepository = $tenantActivatedSettingRepository;
    }

    /**
     * Get mission comments
     *
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function getComments(int $missionId): JsonResponse
    {
        try {
            $comments = $this->missionCommentRepository->getComments($missionId);
            $apiData = $comments;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($apiData->count() > 0) ? trans('messages.success.MESSAGE_MISSION_COMMENT_LISTING')
            : trans('messages.success.MESSAGE_NO_MISSION_COMMENT_FOUND');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
    }

    /**
     * Store mission comment
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "comment" => "required|max:280",
                "mission_id" => "required|integer|exists:mission,mission_id,deleted_at,NULL"
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_COMMENT_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        // Need to check activated setting for comment approval status
        $isAutoApproved = $this->tenantActivatedSettingRepository->checkTenantSettingStatus(
            config('constants.tenant_settings.MISSION_COMMENT_AUTO_APPROVED'),
            $request
        );
        if ($isAutoApproved) {
            $request->request->add(
                [
                    'approval_status' => config('constants.comment_approval_status.PUBLISHED')
                ]
            );
        }
        $missionComment = $this->missionCommentRepository->store($request->auth->user_id, $request->toArray());

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiData = ['comment_id' => $missionComment->comment_id];
        $apiMessage = ($isAutoApproved) ? trans('messages.success.MESSAGE_AUTO_APPROVED_COMMENT_ADDED') :
        trans('messages.success.MESSAGE_COMMENT_ADDED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
