<?php
namespace App\Http\Controllers\App\StoryVisitor;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\StoryVisitor;
use App\Repositories\StoryVisitor\StoryVisitorRepository;
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
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new Story Visitor controller instance
     *
     * @param App\Repositories\StoryVisitor\StoryVisitorRepository;
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
}
