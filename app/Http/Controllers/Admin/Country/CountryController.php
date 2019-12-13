<?php
namespace App\Http\Controllers\Admin\Country;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Repositories\Country\CountryRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Repositories\CountryLanguage\CountryLanguageRepository;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use App\Events\User\UserActivityLogEvent;
use App\Helpers\LanguageHelper;
use Illuminate\Validation\Rule;

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
     * @var App\Repositories\CountryLanguage\CountryLanguageRepository
     */
    private $countryLanguageRepository;

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
     * @param App\Repositories\CountryLanguage\CountryLanguageRepository $countryLanguagRepository
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(
        CountryRepository $countryRepository,
        CountryLanguageRepository $countryLanguagRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Request $request
    ) {
        $this->countryRepository = $countryRepository;
        $this->countryLanguageRepository = $countryLanguagRepository;
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
     * Store a newly created resource.
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
                "countries.*.iso" => 'required|max:3|unique:country,ISO,NULL,country_id,deleted_at,NULL',
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
                config('constants.error_codes.ERROR_COUNTRY_INVALID_DATA'),
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
            // Add all translations add into country_translation table
            $createdCountries[$key]['country_id'] = $country['country_id'] = $countryDetails->country_id;
            
            $this->countryLanguageRepository->store($languages, $country);
        }

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

    /**
     * Update resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->countryRepository->find($id);
            // Server side validations
            $validator = Validator::make(
                $request->all(),
                [
                    "iso" => 'required|max:3|unique:country,ISO,NULL,country_id,deleted_at,NULL',
                    "iso" => [
                        "sometimes",
                        "required",
                        "max:3",
                        Rule::unique('country')->ignore($id, 'country_id')],
                    "translations" => 'required|array',
                    "translations.*.lang" => 'required|min:2|max:2',
                    "translations.*.name" => 'required'
                ]
            );
            
            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_COUNTRY_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
            // Get all countries
            $languages = $this->languageHelper->getLanguages($request);

            $this->countryRepository->update($request, $id);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_COUNTRY_UPDATED');
                    
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.COUNTRY'),
                config('constants.activity_log_actions.UPDATED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                $request->toArray(),
                null,
                null
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_COUNTRY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_COUNTRY_NOT_FOUND')
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->countryRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_COUNTRY_DELETED');

            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.COUNTRY'),
                config('constants.activity_log_actions.DELETED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                null,
                null,
                $id
            ));
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_COUNTRY_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_COUNTRY_NOT_FOUND')
            );
        }
    }
}
