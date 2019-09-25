<?php
namespace App\Http\Controllers\App\Mission;

use App\Repositories\Mission\MissionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Traits\RestExceptionHandlerTrait;
use Validator;

class MissionRatingController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var MissionRepository
     */
    private $missionRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Mission rating controller instance.
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
     * Store a newly created resource in storage.
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
                "rating" => "required|numeric|min:0.5|max:5",
                "mission_id" => "integer|required|exists:mission,mission_id,deleted_at,NULL"
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MISSION_RATING_INVALID_DATA'),
                $validator->errors()->first()
            );
        }
        
        // Store mission rating
        $missionRating = $this->missionRepository->storeMissionRating($request->auth->user_id, $request->toArray());

        // Set response data
        $apiStatus = ($missionRating->wasRecentlyCreated) ? Response::HTTP_CREATED : Response::HTTP_OK;
        $apiMessage = ($missionRating->wasRecentlyCreated) ? trans('messages.success.MESSAGE_RATING_ADDED')
        : trans('messages.success.MESSAGE_RATING_UPDATED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
