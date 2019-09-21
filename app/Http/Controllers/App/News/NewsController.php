<?php
namespace App\Http\Controllers\App\News;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\News\NewsRepository;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use App\Models\News;
use App\Transformations\NewsTransformable;
use App\Helpers\LanguageHelper;
use App\Helpers\Helpers;

//!  News Controller
/*!
This controller is responsible for handling news show and listing operation.
 */
class NewsController extends Controller
{
    use RestExceptionHandlerTrait, NewsTransformable;
    /**
     * @var App\Repositories\News\NewsRepository
     */
    private $newsRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\News\NewsRepository $newsRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        NewsRepository $newsRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers
    ) {
        $this->newsRepository = $newsRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
    }

    /**
     * Display listing of news
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->languageHelper->getLanguageId($request);
            $news = $this->newsRepository->getNewsList(
                $request,
                $languageId,
                config('constants.news_status.PUBLISHED')
            );
            $newsTransform = $news
            ->map(function (News $newsTransform) {
                return $this->getTransformedNews($newsTransform, true);
            })->all();

            $requestString = $request->except(['page','perPage']);
            $newsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $newsTransform,
                $news->total(),
                $news->perPage(),
                $news->currentPage(),
                [
                    'path' => $request->url().'?'.http_build_query($requestString),
                    'query' => [
                        'page' => $news->currentPage()
                    ]
                ]
            );

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiData = $newsPaginated;
            $apiMessage = ($news->isEmpty()) ? trans('messages.custom_error_message.ERROR_NEWS_NOT_FOUND')
            : trans('messages.success.MESSAGE_NEWS_LISTING');

            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display news details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $newsId
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request, int $newsId): JsonResponse
    {
        try {
            $languageId = $this->languageHelper->getLanguageId($request);
            // Get news details
            $news = $this->newsRepository
            ->getNewsDetails($newsId, $languageId, config('constants.news_status.PUBLISHED'));
            // Transform news details
            $newsTransform = $this->getTransformedNews($news);
            
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_NEWS_FOUND');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $newsTransform);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_NEWS_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_NEWS_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
