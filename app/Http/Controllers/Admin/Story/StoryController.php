<?php
namespace App\Http\Controllers\Admin\Story;

use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Repositories\Story\StoryRepository;
use App\Repositories\User\UserRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Transformations\StoryTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Validator;

class StoryController extends Controller
{
    use RestExceptionHandlerTrait, StoryTransformable;

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
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\Story\StoryRepository $storyRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        StoryRepository $storyRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper
    ) {
        $this->userRepository = $userRepository;
        $this->storyRepository = $storyRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $userId
     * @param Illuminate\Http\Request $request
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

        $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
        $language = $this->languageHelper->getLanguageDetails($request);
<<<<<<< HEAD
                
        $userStories = $this->storyRepository->getUserStoriesWithPagination($request, $userId);
=======
        
        $userStories = $this->storyRepository->getUserStoriesWithPagination($request, $language->language_id, $userId);
>>>>>>> 46bb7c8cf7cab5f60dc6769a4d76ab4958106596

        $storyTransformed = $userStories
            ->getCollection()
            ->map(function ($story) use ($request, $defaultTenantLanguage, $language) {
                $story = $this->transformStory($story, $defaultTenantLanguage->language_id, $language->language_id);
                return $story;
            });

        $requestString = $request->except(['page', 'perPage']);
        $storyPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $storyTransformed,
            $userStories->total(),
            $userStories->perPage(),
            $userStories->currentPage(),
            [
                'path' => $request->url() . '?' . http_build_query($requestString),
                'query' => [
                    'page' => $userStories->currentPage(),
                ],
            ]
        );

        $apiData = $storyPaginated;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($apiData->count()) ?
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
