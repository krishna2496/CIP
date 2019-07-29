<?php

namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Mission\MissionRepository;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MissionMediaController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var MissionRepository
     */
    private $missionRepository;
    
    /**
     * @var ResponseHelper
     */
    private $responseHelper;
       
    /**
     * Create a new  Mission media controller instance
     *
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper
    ) {
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Get mission media listing
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function getMissionMedia(Request $request, int $missionId): JsonResponse
    {
        try {
            $missionMedia = $this->missionRepository->getMissionMedia($missionId);
            $apiData = $missionMedia->toArray();
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (!empty($apiData)) ?
            trans('messages.success.MESSAGE_MISSION_MEDIA_LISTING') :
            trans('messages.success.MESSAGE_NO_MISSION_MEDIA_FOUND');
            return $this->responseHelper->success(
                $apiStatus,
                $apiMessage,
                $apiData
            );
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
