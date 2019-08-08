<?php

namespace App\Http\Controllers\Admin\Mission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Http\Response;
use App\Repositories\MissionComment\MissionCommentRepository;
use App\Helpers\ResponseHelper;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Mission\MissionRepository;

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
     * @var MissionRepository
     */
    private $missionRepository;

    /**
     * Create a new comment controller instance
     *
     * @param App\Repositories\Mission\MissionCommentRepository $missionCommentRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionRepository $missionRepository,
        MissionCommentRepository $missionCommentRepository,
        ResponseHelper $responseHelper
    ) {
        $this->missionRepository = $missionRepository;
        $this->missionCommentRepository = $missionCommentRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Get listing of tenant's comments
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function index(int $missionId): JsonResponse
    {
        try {
            $apiData = $this->missionCommentRepository->getComments($missionId);
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($apiData->count()) ?
            trans('messages.success.MESSAGE_MISSION_COMMENT_LISTING') :
            trans('messages.success.MESSAGE_NO_MISSION_COMMENT_FOUND');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(int $missionId, int $commentId): JsonResponse
    {
        // First find mission
        try {
            $mission = $this->missionRepository->find($missionId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
        // Now find comments from that mission
        try {
            $apiData = $this->missionCommentRepository->getComment($commentId);
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_COMMENT_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData->toArray());
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_COMMENT_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_COMMENT_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
