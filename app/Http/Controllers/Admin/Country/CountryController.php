<?php
namespace App\Http\Controllers\Admin\Country;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Repositories\Country\CountryRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Repositories\CountryTranslation\CountryTranslationRepository;
use InvalidArgumentException;
use Validator;
use App\Events\User\UserActivityLogEvent;
use App\Helpers\LanguageHelper;

//!  Country controller
/*!
This controller is responsible for handling country listing operation.
 */
class CountryController extends Controller
{
    use RestExceptionHandlerTrait;

    /**
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;

    /**
     * @var App\Repositories\CountryTranslation\CountryTranslationRepository
     */
    private $countryTranslationRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var string
     */
    private $userApiKey;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Country\CountryRepository $countryRepository
     * @param Illuminate\Helpers\ResponseHelper $responseHelper
     * @param App\Repositories\CountryTranslation\CountryTranslationRepository $countryTranslationRepository
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(
        CountryRepository $countryRepository,
        CountryTranslationRepository $countryTranslationRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Request $request
    ) {
        $this->countryRepository = $countryRepository;
        $this->countryTranslationRepository = $countryTranslationRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->userApiKey =$request->header('php-auth-user');
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

    /**
     * Store a newly created availability.
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
                "countries" => 'required',
                "countries.*.iso" => 'required',
                "countries.*.translations" => 'required|array',
                "countries.*.translations.*.lang" => 'required|min:2|max:2',
                "countries.*.translations.*.name" => 'required'
            ]
        );
        
        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_AVAILABILITY_INVALID_DATA'),
                $validator->errors()->first()
            );
        }
        // Get all countries
        $languages = $this->languageHelper->getLanguages($request);

        // Add countries one by one
        $createdCountries = [];
        foreach ($request->countries as $key => $country) {

            // Add country ISO into country table
            $countryDetails = $this->countryRepository->store($country['iso']);
            // dd($countryDetails);
            // Add all translations add into country_translation table
            $createdCountries[$key]['country_id'] = $country['country_id'] = $countryDetails->country_id;
            
            $this->countryTranslationRepository->store($languages, $country);
        }
        // dd($createdCountries);
        

        // Set response data
        $apiData = ['country_ids' => $createdCountries];
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_COUNTRY_CREATED');
                
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.COUNTRY'),
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
}
