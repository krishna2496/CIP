<?php
namespace App\Http\Controllers\App\StoryVisitor;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\StoryVisitor;
use App\Repositories\StoryVisitor\StoryVisitorRepository;
use App\Repositories\Story\StoryRepository;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoryVisitorController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\StoryVisitor\StoryVisitorRepository;
     */
    private $storyVisitorRepository;

    /**
     * @var App\Repositories\Story\StoryRepository;
     */
    private $storyRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Story Visitor controller instance
     *
     * @param pp\Repositories\StoryVisitor\StoryVisitorRepository;
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        StoryVisitorRepository $storyVisitorRepository,
        StoryRepository $storyRepository,
        ResponseHelper $responseHelper
    ) {
        $this->storyVisitorRepository = $storyVisitorRepository;
        $this->storyRepository = $storyRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Store story visitor details
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $storyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $storyId): JsonResponse
    {
        // check for story exist or not
        try {
            $story = $this->storyRepository
                ->checkStoryExist($storyId);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_STORY_NOT_FOUND')
            );
        }

        // check for story published, then only store story visitor data
        try {
            $story = $this->storyRepository
                ->getStoryDetails($storyId, config('constants.story_status.PUBLISHED'));
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_PUBLISHED_STORY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_PUBLISHED_STORY_NOT_FOUND')
            );
        }

        // Store story visitor data
        $storyVisitor = $this->storyVisitorRepository->store($request, $storyId);

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_STORY_VISITOR_ADDED_SUCESSFULLY');
        $apiData = ['story_visitor_id' => $storyVisitor->story_visitor_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
