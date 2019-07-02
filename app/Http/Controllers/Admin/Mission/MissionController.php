<?php
namespace App\Http\Controllers\Admin\Mission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use App\Repositories\Mission\MissionRepository;
use App\Models\Mission;
use App\Models\MissionLanguage;
use App\Models\MissionDocument;
use App\Models\MissionMedia;
use App\Models\MissionTheme;
use App\Models\MissionApplication;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use Validator;
use DB;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;
use InvalidArgumentException;

class MissionController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Mission\MissionRepository
     */
    private $missionRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\Mission\MissionRepository $missionRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(MissionRepository $missionRepository, ResponseHelper $responseHelper)
    {
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of Mission.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get mission
            $missions = $this->missionRepository->missionList($request);

            // Set response data
            $apiData = $missions;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($missions->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
             : trans('messages.success.MESSAGE_MISSION_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "theme_id" => "required",
                "mission_type" => ['required', Rule::in(config('constants.mission_type'))],
                "location" => "required",
                "location.city_id" => "required",
                "location.country_code" => "required",
                "mission_detail" => "required",
                "mission_detail.*.lang" => "required",
                "mission_detail.*.title" => "required",
                "organisation" => "required",
                "publication_status" => ['required', Rule::in(config('constants.publication_status'))],
                "goal_objective" => "required_if:mission_type,GOAL",
                "media_images.*.media_name" => "required",
                "media_images.*.media_type" => Rule::in(config('constants.image_types')),
                "media_images.*.media_path" => "required",
                "media_videos.*.media_name" => "required",
                "media_videos.*.media_path" => "required",
                "documents.*.document_name" => "required",
                "documents.*.document_type" => Rule::in(config('constants.document_types')),
                "documents.*.document_path" => "required",
                "start_date" => "before:end_date",
                "end_date" => "after:start_date",
                "total_seats" => "numeric"
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_MISSION_DATA'),
                $validator->errors()->first()
            );
        }

        try {
            $mission = $this->missionRepository->store($request);
                       
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_ADDED');
            $apiData = ['mission_id' => $mission->mission_id];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display the specified mission detail.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Get data for parent table
            $mission = $this->missionRepository->find($id);
            
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $mission->toArray());
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_NO_MISSION_FOUND'),
                trans('messages.custom_error_message.ERROR_NO_MISSION_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "mission_type" => [Rule::in(config('constants.mission_type'))],
                "location.city_id" => "required_with:location",
                "location.country_code" => "required_with:location",
                "mission_detail.*.lang" => "required_with:mission_detail",
                "mission_detail.*.title" => "required_with:mission_detail",
                "publication_status" => [Rule::in(config('constants.publication_status'))],
                "goal_objective" => "required_if:mission_type,GOAL",
                "media_images.*.media_name" => "required_with:media_images",
                "media_images.*.media_type" => ['required_with:media_images',
                 Rule::in(config('constants.image_types'))],
                "media_images.*.media_path" => "required_with:media_images",
                "media_videos.*.media_name" => "required_with:media_videos",
                "media_videos.*.media_path" => "required_with:media_videos",
                "documents.*.document_name" => "required_with:documents",
                "documents.*.document_type" => ['required_with:documents',
                 Rule::in(config('constants.document_types'))],
                "documents.*.document_path" => "required_with:documents",
                "start_date" => "before:end_date",
                "end_date" => "after:start_date",
                "total_seats" => "numeric"
            ]
        );
        
        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        
        try {
            $this->missionRepository->update($request, $id);
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_UPDATED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $mission = $this->missionRepository->delete($id);

            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (\Exception $e) {
            return $this->responseHelper->error(
                Response::HTTP_FORBIDDEN,
                Response::$statusTexts[Response::HTTP_FORBIDDEN],
                config('constants.error_codes.ERROR_MISSION_DELETION'),
                trans('messages.custom_error_message.ERROR_MISSION_DELETION')
            );
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function missionApplications(Request $request, int $missionId): JsonResponse
    {
        try {
            $applicationList = $this->missionRepository->missionApplications($request, $missionId);
            $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATION_LISTING')
             : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return $this->responseHelper->successWithPagination(
                Response::HTTP_OK,
                $responseMessage,
                $applicationList
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display specified resource.
     *
     * @param int $missionId
     * @param int $applicationId
     * @return Illuminate\Http\JsonResponse
     */
    public function missionApplication(int $missionId, int $applicationId): JsonResponse
    {
        try {
            $applicationList = $this->missionRepository->missionApplication($missionId, $applicationId);
            $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATION_LISTING')
             : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return $this->responseHelper->success(Response::HTTP_OK, $responseMessage, $applicationList);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Update resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @param int $applicationId
     * @return Illuminate\Http\JsonResponse
     */
    public function updateApplication(Request $request, int $missionId, int $applicationId): JsonResponse
    {
        try {
            $validator = Validator::make($request->toArray(), [
                'approval_status' => Rule::in(config('constants.application_status')),
            ]);

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_INVALID_MISSION_APPLICATION_DATA'),
                    $validator->errors()->first()
                );
            }

            $application = $this->missionRepository->updateApplication($request, $missionId, $applicationId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_APPLICATION_UPDATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
