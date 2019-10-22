<?php
namespace App\Http\Controllers\Admin\Country;

use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Repositories\Country\CountryRepository;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;

class CountryController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Country\CountryRepository $countryRepository
     * @param Illuminate\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        CountryRepository $countryRepository,
        ResponseHelper $responseHelper
    ) {
        $this->countryRepository = $countryRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
    * Get country list
    *
    * @return Illuminate\Http\JsonResponse
    */
    public function index() : JsonResponse
    {
        $countryList = $this->countryRepository->countryList();
        $apiData = $countryList->toArray();
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (!empty($apiData)) ?
        trans('messages.success.MESSAGE_COUNTRY_LISTING') :
        trans('messages.success.MESSAGE_NO_COUNTRY_FOUND');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
