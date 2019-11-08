<?php
namespace App\Http\Controllers\Admin\Availability;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Availability\AvailabilityRepository;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Availability\AvailabilityRepository;
     */
    private $availabilityRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new availability controller instance
     *
     * @param App\Repositories\Availability\AvailabilityRepository;
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        AvailabilityRepository $availabilityRepository,
        ResponseHelper $responseHelper
    ) {
        $this->availabilityRepository = $availabilityRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Display a listing of availability.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Get availability lists
        $availabilityLists = $this->availabilityRepository->getAvailabilityList($request);

        // Set response data
        $apiData = $availabilityLists;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($availabilityLists->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
            : trans('messages.success.MESSAGE_AVAILABILITY_LISTING');
        return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
    }

}
