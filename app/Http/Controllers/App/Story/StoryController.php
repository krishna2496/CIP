<?php
namespace App\Http\Controllers\App\Story;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Story\StoryRepository;
use App\Models\Story;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Validator;

class StoryController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Story\StoryRepository
     */
    private $storyRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new Story controller instance
     *
     * @param App\Repositories\Story\StoryRepository $storyRepository
     * @param Illuminate\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        StoryRepository $storyRepository,
        ResponseHelper $responseHelper
    ) {
        $this->storyRepository = $storyRepository;
        $this->responseHelper = $responseHelper;
    }
       
    /**
     * Store story details
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try { 
            $validator = Validator::make(
                $request->toArray(),
                [
                    'mission_id' => 'required|exists:mission,mission_id,deleted_at,NULL',
                    'title' => 'required|max:255',
                    'story_images' => 'max:'.config("constants.STORY_MAX_IMAGE_LIMIT"),
                    'story_images.*' => 'max:'.config("constants.STORY_IMAGE_SIZE_LIMIT").'|valid_story_image_type',
                    'story_videos' => 'valid_story_video_url|max_video_url',
                    'description' => 'required|max:40000'
                ]
            );
            
            // If validator fails
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_STORY_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            $storyVideos = explode(",", $request->story_videos); 
            $request->request->add(["story_videos" => $storyVideos]);
          
            // Store story data 
            $storyData = $this->storyRepository->store($request);

            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.STORY_ADDED_SUCESSFULLY');
            $apiData = ['story_id' => $storyData->story_id];

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
