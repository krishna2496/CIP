<?php
namespace App\Http\Controllers\Admin\City;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\City\CityRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\LanguageHelper;
use App\Repositories\CityTranslation\CityTranslationRepository;
use Illuminate\Http\Request;
use Validator;
use App\Events\User\UserActivityLogEvent;

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
     * @var App\Repositories\CityTranslation\CityTranslationRepository
     */
    private $cityTranslationRepository;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\City\CityRepository $cityRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Repositories\CityTranslation\CityTranslationRepository $cityTranslationRepository
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(
        CityRepository $cityRepository,
        ResponseHelper $responseHelper,        
        LanguageHelper $languageHelper,
        CityTranslationRepository $cityTranslationRepository,
        Request $request
    ) {
        $this->cityRepository = $cityRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;        
        $this->cityTranslationRepository = $cityTranslationRepository;        
        $this->userApiKey = $request->header('php-auth-user');
    }

    /**
    * Fetch city by country id
    *
    * @param int $countryId
    * @return Illuminate\Http\JsonResponse
    */
    public function fetchCity(int $countryId): JsonResponse
    {
        try {
            $cityList = $this->cityRepository->cityList($countryId);
            $apiData = $cityList->toArray();
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
    
    /**
     * Store a newly created cities.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {        
        // Server side validations
        $validator = Validator::make(
            $request->all(),
            [
                "country_id" => 'required|exists:country,country_id,deleted_at,NULL',
                "cities" => 'required',
                "cities.*.translations" => 'required|array',
                "cities.*.translations.*.lang" => 'required|min:2|max:2',
                "cities.*.translations.*.name" => 'required'
            ]
        );
        
        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_CITY_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        // Get all languages
        $languages = $this->languageHelper->getLanguages($request);

        $countryId = $request->country_id; 

        // Add cities one by one
        $createdCity = [];
        foreach ($request->cities as $key => $city) {
            // Add country id into city table
            $cityDetails = $this->cityRepository->store($countryId);

            // Add all translations add into city_translation table
            $createdCity[$key]['city_id'] = $city['city_id'] = $cityDetails->city_id;
            
            $this->cityTranslationRepository->store($languages, $city);
        }

        // Set response data
        $apiData = ['city_ids' => $createdCity];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_CITY_CREATED');
                
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.CITY'),
            config('constants.activity_log_actions.CREATED'),
            config('constants.activity_log_user_types.API'),
            $this->userApiKey,
            get_class($this),
            $request->toArray(),
            null,
            null
        ));

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Fetch all city
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $cityList = $this->cityRepository->cityLists();
        $apiData = $cityList->toArray();
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (!empty($apiData)) ? trans('messages.success.MESSAGE_CITY_LISTING')
        : trans('messages.success.MESSAGE_NO_CITY_FOUND');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
