<?php
namespace App\Http\Controllers\Admin\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MissionApplication\MissionApplicationRepository;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use Illuminate\Validation\Rule;

class MissionApplicationController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var MissionApplicationRepository
     */
    private $missionApplicationRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Mission controller instance.
     *
     * @param App\Repositories\MissionApplication\MissionApplicationRepository $missionApplicationRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionApplicationRepository $missionApplicationRepository,
        ResponseHelper $responseHelper
    ) {
        $this->missionApplicationRepository = $missionApplicationRepository;
        $this->responseHelper = $responseHelper;
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
            $applicationList = $this->missionApplicationRepository->missionApplications($request, $missionId);
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
            $applicationList = $this->missionApplicationRepository->missionApplication($missionId, $applicationId);
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

            $application = $this->missionApplicationRepository->updateApplication($request, $missionId, $applicationId);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_APPLICATION_UPDATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_APPLICATION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_APPLICATION_NOT_FOUND')
            );
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
