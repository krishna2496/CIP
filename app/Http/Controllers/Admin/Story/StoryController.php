<?php
namespace App\Http\Controllers\Admin\Story;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\ResponseHelper;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Story\StoryRepository;
use Validator;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use App\Transformations\StoryTransformable;

class StoryController extends Controller
{
    use RestExceptionHandlerTrait,StoryTransformable;

    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;

    /**
     * @var App\Repositories\Story\StoryRepository
     */
    private $storyRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Story\StoryRepository $storyRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        StoryRepository $storyRepository,
        ResponseHelper $responseHelper
    ) {
        $this->userRepository = $userRepository;
        $this->storyRepository = $storyRepository;
        $this->responseHelper = $responseHelper;
    }


    /**
     * Display a listing of the resource.
     *
     * @param int $userId
     * @return Illuminate\Http\JsonResponse
     */
    public function index(int $userId, Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->find($userId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }
        
        $userStories = $this->storyRepository->getUserStoriesWithPagination($request, $userId);
        $responceData = [];
        $storyTransformed = $userStories
        ->getCollection()
        ->map(function ($story) use ($request){
        $story = $this->transformStory($story); 
            return $story;
        });

        $requestString = $request->except(['page','perPage']);
        $storyPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
        	$storyTransformed,
        	$userStories->total(),
        	$userStories->perPage(),
        	$userStories->currentPage(),
        	[
        		'path' => $request->url().'?'.http_build_query($requestString),
        		'query' => [
        			'page' => $userStories->currentPage()
        			]
        		]
        	);
        
        
        $apiData = $storyPaginated;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (!empty($apiData)) ?
        trans('messages.success.MESSAGE_STORIES_ENTRIES_LISTING') :
        trans('messages.success.MESSAGE_NO_STORIES_ENTRIES_FOUND');
        
        return $this->responseHelper->successWithPagination(
        		$apiStatus,
        		$apiMessage,
        		$apiData,
        		[]
        	);
    }

    /**
     * Publish/decline Story entry
     *
     * @param \Illuminate\Http\Request $request
     * @param int  $storyId
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $storyId): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "status" => ['required', Rule::in(config('constants.story_status'))],
                ]
            );	
            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_STORY_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }
            $this->storyRepository->getStoryDetails($storyId);
            $this->storyRepository->updateStoryStatus($request->status, $storyId);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_STORY_STATUS_UPDATED');
            $apiData = ['story_id' => $storyId];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }
    }
}
