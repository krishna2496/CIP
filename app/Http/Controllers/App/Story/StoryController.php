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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @param App\Helpers\ResponseHelper $responseHelper
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

        if ($request->has('story_videos')) {
            $storyVideos = explode(",", $request->story_videos);
            $request->request->add(["story_videos" => $storyVideos]);
        }
        
        // Store story data 
        $storyData = $this->storyRepository->store($request);

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.STORY_ADDED_SUCESSFULLY');
        $apiData = ['story_id' => $storyData->story_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Remove the story details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int  $storyId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $storyId): JsonResponse
    {
        try {
            $this->storyRepository->delete($storyId, $request->auth->user_id);
           
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_STORY_DELETED');

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }
    }
}
