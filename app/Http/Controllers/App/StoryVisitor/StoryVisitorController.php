<?php
namespace App\Http\Controllers\App\StoryVisitor;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\StoryVisitor;
use App\Repositories\StoryVisitor\StoryVisitorRepository;
use App\Traits\RestExceptionHandlerTrait;
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
        ResponseHelper $responseHelper
    ) {
        $this->storyVisitorRepository = $storyVisitorRepository;
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
        // Store story visitor data
        $storyVisitor = $this->storyVisitorRepository->store($request, $storyId);

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_STORY_VISITOR_ADDED_SUCESSFULLY');
        $apiData = ['story_visitor_id' => $storyVisitor->story_visitor_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
