<?php
namespace App\Http\Controllers\App\City;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\City\CityRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\LanguageHelper;

//!  City controller
/*!
This controller is responsible for handling city listing operation.
 */
class CityController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\City\CityRepository
     */
    private $cityRepository;

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
     * @param App\Repositories\City\CityRepository $cityRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param Illuminate\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        CityRepository $cityRepository,
        ResponseHelper $responseHelper,        
        LanguageHelper $languageHelper
    ) {
        $this->cityRepository = $cityRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
    }

    /**
    * Fetch city by country id
    *
    * @param Illuminate\Http\Request $request
    * @param int $countryId
    * @return Illuminate\Http\JsonResponse
    */
    public function fetchCity(Request $request, int $countryId): JsonResponse
    {
        try {
            // Get language id
            $languageId = $this->languageHelper->getLanguageId($request);
        
            // Fetch city lists
            $cityList = $this->cityRepository->cityList($countryId);
            
            if (!$cityList->isEmpty()) {
                // Transform city details
                $cityDetails = $this->cityRepository->cityTransform($cityList->toArray(), $languageId);
            }
            $apiData = isset($cityDetails) ? $cityDetails : $cityList->toArray();
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (!empty($apiData)) ? trans('messages.success.MESSAGE_CITY_LISTING')
            : trans('messages.success.MESSAGE_NO_CITY_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_COUNTRY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_COUNTRY_NOT_FOUND')
            );
        }
    }
}
