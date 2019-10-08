<?php
namespace App\Http\Controllers\App\Story;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Story\StoryRepository;
use App\Models\Story;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use App\Http\Controllers\Controller;
use App\Helpers\ExportCSV;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use App\Transformations\StoryTransformable;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Helpers;

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
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;
    
    /**
     * Create a new Story controller instance
     *
     * @param App\Repositories\Story\StoryRepository $storyRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        StoryRepository $storyRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers,
        LanguageHelper $languageHelper
    ) {
        $this->storyRepository = $storyRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
        $this->languageHelper = $languageHelper;
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
                'story_images.*' => 'valid_story_image_type|max:'.config("constants.STORY_IMAGE_SIZE_LIMIT"),
                'story_videos' => 'valid_story_video_url|max_video_url|sometimes|required',
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
        
        // Store story data
        $storyData = $this->storyRepository->store($request);

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.STORY_ADDED_SUCESSFULLY');
        $apiData = ['story_id' => $storyData->story_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Update story details
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $storyId
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $storyId): JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->toArray(),
                [
                    'mission_id' => 'sometimes|required|exists:mission,mission_id,deleted_at,NULL',
                    'title' => 'sometimes|required|max:255',
                    'story_images' => 'max:'.config("constants.STORY_MAX_IMAGE_LIMIT"),
                    'story_images.*' => 'valid_story_image_type|max:'.config("constants.STORY_IMAGE_SIZE_LIMIT"),
                    'story_videos' => 'valid_story_video_url|max_video_url',
                    'description' => 'sometimes|required|max:40000'
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
    
            $storyStatus = array(config('constants.story_status.PUBLISHED'),
            config('constants.story_status.DECLINED'));

            // Check if approved or declined story
            $validStoryStatus = $this->storyRepository->checkStoryStatus(
                $request->auth->user_id,
                $storyId,
                $storyStatus
            );

            if (!$validStoryStatus) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_STORY_PUBLISHED_OR_DECLINED'),
                    trans('messages.custom_error_message.ERROR_STORY_PUBLISHED_OR_DECLINED')
                );
            }

            // Update story data
            $storyData = $this->storyRepository->update($request, $storyId);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_STORY_UPDATED');
            $apiData = ['story_id' => $storyData->story_id];
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }
    }

    /**
     * Remove story details.
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
            // Get Story details
            $story = $this->storyRepository
            ->getStoryDetails($storyId, config('constants.story_status.PUBLISHED'));
            
            $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
            $language = $this->languageHelper->getLanguageDetails($request);

            // Transform news details
            $storyTransform = $this->transformStory($story, $defaultTenantLanguage->language_id, $language->language_id)
            ->toArray();
            
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
            // Do copy of declined story
            $newStoryId = $this->storyRepository->doCopyDeclinedStory($storyId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }

        try {
            // check declined story details by story id
            $this->storyRepository
            ->getStoryDetails($storyId, config('constants.story_status.DECLINED'));
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_DECLINED_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_DECLINED_STORY_NOT_FOUND')
            );
        }
        
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_STORY_COPIED_SUCCESS');
        $apiData = ['story_id' => $newStoryId ];
            
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
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
    	$language = $this->languageHelper->getLanguageDetails($request);
        $storyList = $this->storyRepository->getUserStories($language->language_id, $request->auth->user_id);
        
        if ($storyList->count()==0) {
        	$apiStatus = Response::HTTP_OK;
        	$apiMessage =  trans('messages.success.MESSAGE_UNABLE_TO_EXPORT_USER_STORIES_ENTRIES');
        	return $this->responseHelper->success($apiStatus, $apiMessage);
        }
        
        $fileName = config('constants.export_story_file_names.STORY_XLSX');
        $excel = new ExportCSV($fileName);
        $headings = [
        	trans("messages.export_story_headings.STORY_TITLE"),
        	trans("messages.export_story_headings.STORY_DESCRIPTION"),
        	trans("messages.export_story_headings.STORY_STATUS"),
        	trans("messages.export_story_headings.MISSION"),
        	trans("messages.export_story_headings.PUBLISH_DATE"),
        ];
        
        $excel->setHeadlines($headings);
        foreach ($storyList as $story) {
        	$excel->appendRow([
        		$story->title,
        		$story->description,
        		$story->status,
        		$story->mission->missionLanguage[0]->title,
        		$story->published_at
        	]);
        }
        
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $path = $excel->export('app/'.$tenantName.'/story/'.$request->auth->user_id.'/exports');
        return response()->download($path, $fileName);
    }

    /**
     * Submit story details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $storyId
     * @return Illuminate\Http\JsonResponse
     */
    public function submitStory(Request $request, int $storyId): JsonResponse
    {
        try {
            $storyStatus = array(
                config('constants.story_status.PUBLISHED'),
                config('constants.story_status.DECLINED')
            );

            // User can't submit story if its published or declined
            $validStoryStatus = $this->storyRepository->checkStoryStatus(
                $request->auth->user_id,
                $storyId,
                $storyStatus
            );

            if (!$validStoryStatus) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SUBMIT_STORY_PUBLISHED_OR_DECLINED'),
                    trans('messages.custom_error_message.ERROR_SUBMIT_STORY_PUBLISHED_OR_DECLINED')
                );
            }

            // Submit story
            $storyData = $this->storyRepository->submitStory($request->auth->user_id, $storyId);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_STORY_SUBMITTED_SUCESSFULLY');
            $apiData = ['story_id' => $storyData->story_id];
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }
    }

    /**
     * Delete story image.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $storyId
     * @param int $mediaId
     * @return Illuminate\Http\JsonResponse
     */
    public function deleteStoryImage(Request $request, int $storyId, int $mediaId): JsonResponse
    {
        try {
            // Fetch story data
            $storyData = $this->storyRepository->findStoryByUserId($request->auth->user_id, $storyId);
            
            $statusArray = [
                config('constants.story_status.PUBLISHED'),
                config('constants.story_status.DECLINED')
            ];
            
            // User cannot remove story image if story is published or declined            
            if (in_array($storyData->status, $statusArray)) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_STORY_IMAGE_DELETE'),
                    trans('messages.custom_error_message.ERROR_STORY_IMAGE_DELETE')
                );
            }
            
            // Delete story image
            try {
                $storyImage = $this->storyRepository->deleteStoryImage($mediaId, $storyId);
            } catch (ModelNotFoundException $e) {
                return $this->modelNotFound(
                    config('constants.error_codes.ERROR_STORY_IMAGE_NOT_FOUND'),
                    trans('messages.custom_error_message.ERROR_STORY_IMAGE_NOT_FOUND')
                );
            }

            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_STORY_IMAGE_DELETED');

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }
    }
}
