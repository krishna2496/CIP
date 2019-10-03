<?php
namespace App\Http\Controllers\App\Story;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Story\StoryRepository;
use App\Models\Story;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Helpers\ExportCSV;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use App\Transformations\StoryTransformable;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoryController extends Controller
{
    use RestExceptionHandlerTrait,StoryTransformable;
    /**
     * @var App\Repositories\Story\StoryRepository
     */
    private $storyRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;
    
    /**
     * Create a new Story controller instance
     *
     * @param App\Repositories\Story\StoryRepository $storyRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        StoryRepository $storyRepository,
        ResponseHelper $responseHelper,
    	Helpers $helpers
    ) {
        $this->storyRepository = $storyRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
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
    
    /**
     * Display story details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $storyId
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request, int $storyId): JsonResponse
    {
    	try {
    		//$languageId = $this->languageHelper->getLanguageId($request);
    		// Get Story details
    		$story = $this->storyRepository
    		->getStoryDetails($storyId,config('constants.story_status.PUBLISHED'));
    		// Transform news details
    		$storyTransform = $this->transformStory($story)->toArray();
    		
    		$apiStatus = Response::HTTP_OK;
    		$apiMessage = trans('messages.success.MESSAGE_STORY_FOUND');
    
    		return $this->responseHelper->success($apiStatus, $apiMessage, $storyTransform);
    	} catch (ModelNotFoundException $e) {
    		return $this->modelNotFound(
    			config('constants.error_codes.ERROR_PUBLISHED_STORY_NOT_FOUND'),
    			trans('messages.custom_error_message.ERROR_PUBLISHED_STORY_NOT_FOUND')
    		);
    	}
    }
    
    /**
     * Do copy of declined story
     *
     * @param \Illuminate\Http\Request $request
     * @param int $storyId
     * @return Illuminate\Http\JsonResponse
     */
    public function copyStoryAfterDecline(Request $request, int $storyId): JsonResponse
    {
    	try {
    		// check declined story details by story id
    		$this->storyRepository
    		->getStoryDetails($storyId,config('constants.story_status.DECLINED'));
    		// Do copy of declined story
    		$newStoryId = $this->storyRepository->doCopyDeclinedStory($storyId);
    	
    		$apiStatus = Response::HTTP_OK;
    		$apiMessage = trans('messages.success.MESSAGE_STORY_COPIED_SUCCESS');
    		$apiData = ['story_id' => $newStoryId ];
    		
    		return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);   		
    	} catch (ModelNotFoundException $e) {
    		return $this->modelNotFound(
    			config('constants.error_codes.ERROR_DECLINED_STORY_NOT_FOUND'),
    			trans('messages.custom_error_message.ERROR_DECLINED_STORY_NOT_FOUND')
    		);
    	}
    }
    
    
    /**
     * Export user's story
     *
     * @param \Illuminate\Http\Request $request
     * @return Object
     */
    public function exportStory(Request $request): Object
    {
    	//get login user story data
    	$storyList = $this->storyRepository->getUserStoriesWithOutPagination($request,$request->auth->user_id);
    	if ($storyList->count()) {
    		$fileName = config('constants.export_story_file_names.STORY_XLSX');
    
    		$excel = new ExportCSV($fileName);
    
    		$headings = [
    			trans('messages.export_story_headings.STORY_TITLE'),
    			trans('messages.export_story_headings.STORY_DESCRIPTION'),
    			trans('messages.export_story_headings.STORY_STATUS'),
    			trans('messages.export_story_headings.PUBLISH_DATE'),
    		];
    		$excel->setHeadlines($headings);
    		
    		foreach ($storyList as $story) {
    			$excel->appendRow([
    					$story->title,
    					$story->description,
    					$story->status,
    					$story->published_at
    			]);
    		}
    
    		$tenantName = $this->helpers->getSubDomainFromRequest($request);
    		$path = $excel->export('app/'.$tenantName.'/story/'.$request->auth->user_id.'/exports');
    		return response()->download($path, $fileName);
    	}
    
    	$apiStatus = Response::HTTP_OK;
    	$apiMessage =  trans('messages.success.MESSAGE_ENABLE_TO_EXPORT_USER_STORIES_ENTRIES');
    	return $this->responseHelper->success($apiStatus, $apiMessage);
    }
    
    /**
     * Used for get login user's all stories data
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserStories(Request $request): JsonResponse
    {
    	// get user's all story data  
    	$userStories = $this->storyRepository->getUserStoriesWithPagination($request, $request->auth->user_id);
    	
    	$storyTransformedData = $this->transformUserRelatedStory($userStories);
    	
    	$requestString = $request->except(['page','perPage']);
    	$storyPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
    		$storyTransformedData,
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
}
