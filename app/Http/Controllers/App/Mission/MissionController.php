<?php
namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\UserFilter\UserFilterRepository;
use Illuminate\Support\Facades\Config;
use App\Models\Mission;
use App\Repositories\MissionTheme\MissionThemeRepository;
use App\Repositories\Skill\SkillRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\City\CityRepository;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use App\Models\UserFilter;
use App\Transformations\MissionTransformable;
use App\Events\User\UserActivityLogEvent;
use App\Repositories\User\UserRepository;
use App\Repositories\State\StateRepository;

//!  Mission controller
/*!
This controller is responsible for handling mission listing, explore mission, filters,
mission favourite, related mission, get mission detail and get usermissions operations.
 */
class MissionController extends Controller
{
    use RestExceptionHandlerTrait, MissionTransformable;
    /**
     * @var MissionRepository
     */
    private $missionRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * @var App\Repositories\UserFilter\UserFilterRepository
     */
    private $userFilterRepository;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Repositories\MissionTheme\MissionThemeRepository;
     */
    private $themeRepository;

    /**
     * @var App\Repositories\Skill\SkillRepository
     */
    private $skillRepository;

    /**
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;

    /**
     * @var App\Repositories\City\CityRepository
     */
    private $cityRepository;

    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;

    /**
     *@var App\Repositories\State\StateRepository $stateRepository
     */
    private $stateRepository;

    /**
     * Create a new Mission controller instance
     *
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Helpers\ResponseHelper $responseHelper
     * @param Illuminate\Http\UserFilterRepository $userFilterRepository
     * @param Illuminate\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @param App\Repositories\MissionTheme\MissionThemeRepository $themeRepository
     * @param App\Repositories\Skill\SkillRepository $skillRepository
     * @param App\Repositories\Country\CountryRepository $countryRepository
     * @param App\Repositories\City\CityRepository $cityRepository
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\State\StateRepository $stateRepository
     * @return void
     */
    public function __construct(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        UserFilterRepository $userFilterRepository,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        MissionThemeRepository $themeRepository,
        SkillRepository $skillRepository,
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        UserRepository $userRepository,
        StateRepository $stateRepository
    ) {
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
        $this->userFilterRepository = $userFilterRepository;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->themeRepository = $themeRepository;
        $this->skillRepository = $skillRepository;
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->userRepository = $userRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * Get missions listing
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getMissionList(Request $request): JsonResponse
    {
        $language = $this->languageHelper->getLanguageDetails($request);
        $languageId = $language->language_id;
        $languageCode = $language->code;
        $userFilterData = [];

        //Save User search data
        $this->userFilterRepository->saveFilter($request);
        // Get users filter
        $userFilters = $this->userFilterRepository->userFilter($request);
        $filterTagArray = $this->missionFiltersTag($request, $language, $userFilters);
        if ($userFilters !== null) {
            $userFilterData = $userFilters->toArray()["filters"];
        }
        
        // Checking explore mission type is out of list or not
        if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
            $exploreMissionType = $request->input('explore_mission_type');
            $authorizedMissionTypes = array(config('constants.TOP_RECOMMENDED'), config('constants.RANDOM'),
                config('constants.THEME'), config('constants.COUNTRY'), config('constants.ORGANIZATION'),
                config('constants.MOST_RANKED'), config('constants.TOP_FAVOURITE'));
                
            if (!in_array($exploreMissionType, $authorizedMissionTypes)
            ) {
                $metaData['filters'] = $userFilterData;
                $metaData['filters']["tags"] = $filterTagArray;
                $missionsTransformed = [];
                $apiData = new \Illuminate\Pagination\LengthAwarePaginator(
                    $missionsTransformed,
                    0,
                    $request->perPage
                );
                $apiStatus = Response::HTTP_OK;
                $apiMessage = trans('messages.success.MESSAGE_MISSION_LISTING');
                
                return $this->responseHelper->successWithPagination(
                    $apiStatus,
                    $apiMessage,
                    $apiData,
                    $metaData
                );
            }
        }

        $missionList = $this->missionRepository->getMissions($request, $userFilterData);
       
        $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
        $defaultTenantLanguageId = $defaultTenantLanguage->language_id;
        $timezone = $this->userRepository->getUserTimezone($request->auth->user_id);
        $missionsTransformed = $missionList
            ->getCollection()
            ->map(function ($item) use ($languageCode, $languageId, $defaultTenantLanguageId, $timezone) {
                return $this->transformMission($item, $languageCode, $languageId, $defaultTenantLanguageId, $timezone);
            })->toArray();
            
        $requestString = $request->except(['page','perPage']);
        $missionsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $missionsTransformed,
            $missionList->total(),
            $missionList->perPage(),
            $missionList->currentPage(),
            [
                'path' => $request->url().'?'.http_build_query($requestString),
                'query' => [
                    'page' => $missionList->currentPage()
                ]
            ]
        );

        $metaData['filters'] = $userFilterData;
        $metaData['filters']["tags"] = $filterTagArray;
        $apiData = $missionsPaginated;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_MISSION_LISTING');
        return $this->responseHelper->successWithPagination(
            $apiStatus,
            $apiMessage,
            $apiData,
            $metaData
        );
    }

    /**
     * Get explore mission data
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function exploreMission(Request $request): JsonResponse
    {
        $apiData = [];
        // Get language code
        $language = $this->languageHelper->getLanguageDetails($request);
        $languageCode = $language->code;
        $languageId = $language->language_id;
        $defaultLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
        $defaultLanguageId = $defaultLanguage->language_id;
        // Get data by top theme
        $topTheme = $this->missionRepository->exploreMission($request, config('constants.TOP_THEME'));
        // Get data by top country
        $topCountry = $this->missionRepository->exploreMission($request, config('constants.TOP_COUNTRY'));
      
        // Get data by top organization
        $topOrganisation = $this->missionRepository->exploreMission($request, config('constants.TOP_ORGANISATION'));

        // Return data by top theme
        if (!empty($topTheme->toArray())) {
            foreach ($topTheme as $key => $value) {
                if ($value->missionTheme && $value->missionTheme->translations) {
                    $arrayKey = array_search($languageCode, array_column($value->missionTheme->translations, 'lang'));
                    if ($arrayKey  !== '') {
                        $returnData[config('constants.TOP_THEME')][$key]['title'] =
                        $value->missionTheme->translations[$arrayKey]['title'];
                        $returnData[config('constants.TOP_THEME')][$key]['id'] =
                        $value->missionTheme->mission_theme_id;
                        $returnData[config('constants.TOP_THEME')][$key]['theme_name'] =
                        $value->missionTheme->theme_name;
                    }
                }
            }
            $apiData[config('constants.TOP_THEME')] = $returnData[config('constants.TOP_THEME')];
        }

        // Return data by top country
        if (!empty($topCountry->toArray())) {
            foreach ($topCountry as $key => $value) {
                if (isset($value->country)) {
                    $translation = $value->country->languages->toArray();

                    $translationkey = '';
                    $index = array_search($languageId, array_column($translation, 'language_id'));
                    $language = ($index === false) ? $defaultLanguageId : $languageId;
                    $translationkey = array_search($language, array_column($translation, 'language_id'));

                    if ($translationkey !== '' && $value->country) {
                        $returnData[config('constants.TOP_COUNTRY')][$key]['title'] =
                        $translation[$translationkey]['name'];
                    }
                    $returnData[config('constants.TOP_COUNTRY')][$key]['id'] =
                    $value->country->country_id;
                }
            }
            $apiData[config('constants.TOP_COUNTRY')] = $returnData[config('constants.TOP_COUNTRY')];
        }

        // Return data by top organisation
        if (!empty($topOrganisation->toArray())) {
            foreach ($topOrganisation as $key => $value) {
                if ($value->organisation_name !== '') {
                    $returnData[config('constants.TOP_ORGANISATION')][$key]['title'] =
                    $value->organisation_name;
                    $returnData[config('constants.TOP_ORGANISATION')][$key]['id'] =
                    $value->organisation_id;
                }
            }
            $apiData[config('constants.TOP_ORGANISATION')] = $returnData[config('constants.TOP_ORGANISATION')];
        }
        
        $apiStatus = Response::HTTP_OK;
        return $this->responseHelper->success(
            $apiStatus,
            '',
            $apiData
        );
    }

    /**
     * Get filter mission data
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function filters(Request $request): JsonResponse
    {
        $returnData = $apiData = [];
        $language = $this->languageHelper->getLanguageDetails($request);
        $languageCode = $language->code;
        $languageId = $language->language_id;
        $defaultLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
        $defaultLanguageId = $defaultLanguage->language_id;
        // Get Data by country
        $missionCountry = $this->missionRepository->missionFilter($request, config('constants.COUNTRY'));
        // Get Data by top theme
        $missionCity = $this->missionRepository->missionFilter($request, config('constants.CITY'));
        // Get Data by top organization
        $missionTheme = $this->missionRepository->missionFilter($request, config('constants.THEME'));
        // Get Data by skills
        $missionSkill = $this->missionRepository->missionFilter($request, config('constants.SKILL'));
        // Get Data by state        
        $missionState = $this->missionRepository->missionFilter($request, config('constants.STATE'));

        if (!empty($missionCountry->toArray())) {
            foreach ($missionCountry as $key => $value) {
                if (isset($value->country)) {
                    $translation = $value->country->languages->toArray();

                    $translationkey = '';
                    $index = array_search($languageId, array_column($translation, 'language_id'));
                    $language = ($index === false) ? $defaultLanguageId : $languageId;
                    $translationkey = array_search($language, array_column($translation, 'language_id'));

                    if ($translationkey !== '' && $value->country) {
                        $returnData[config('constants.COUNTRY')][$key]['title'] =
                        $translation[$translationkey]['name'];
                    }
                    $returnData[config('constants.COUNTRY')][$key]['id'] =
                    $value->country->country_id;
                    $returnData[config('constants.COUNTRY')][$key]['mission_count'] =
                    $value->mission_count;
                    if (isset($returnData[config('constants.COUNTRY')])) {
                        $apiData[config('constants.COUNTRY')] = $returnData[config('constants.COUNTRY')];
                    }
                }
            }
        }

        if (!empty($missionCity->toArray())) {
            foreach ($missionCity as $key => $value) {
                if (isset($value->city)) {
                    $translation = $value->city->languages->toArray();
                    $translationkey = '';

                    $index = array_search($languageId, array_column($translation, 'language_id'));
                    $language = ($index === false) ? $defaultLanguageId : $languageId;
                    $translationkey = array_search($language, array_column($translation, 'language_id'));

                    if ($translationkey !== '') {
                        $returnData[config('constants.CITY')][$key]['title'] =
                        $translation[$translationkey]['name'];
                    }
                    $returnData[config('constants.CITY')][$key]['id'] =
                    $value->city_id;
                    $returnData[config('constants.CITY')][$key]['mission_count'] =
                    $value->mission_count;
                }
            }
            if (isset($returnData[config('constants.CITY')])) {
                $apiData[config('constants.CITY')] = $returnData[config('constants.CITY')];
            }
        }
        
        if (!empty($missionTheme->toArray())) {
            foreach ($missionTheme as $key => $value) {
                if ($value->missionTheme && $value->missionTheme->translations) {
                    $arrayKey = array_search($languageCode, array_column($value->missionTheme->translations, 'lang'));
                    if ($arrayKey  !== '') {
                        $returnData[config('constants.THEME')][$key]['title'] =
                        $value->missionTheme->translations[$arrayKey]['title'];
                        $returnData[config('constants.THEME')][$key]['id'] =
                        $value->missionTheme->mission_theme_id;
                        $returnData[config('constants.THEME')][$key]['mission_count'] =
                        $value->mission_count;
                    }
                }
                if (isset($returnData[config('constants.THEME')])) {
                    $apiData[config('constants.THEME')] = $returnData[config('constants.THEME')];
                }
            }
        }
        
        if (!empty($missionSkill->toArray())) {
            foreach ($missionSkill as $key => $value) {
                if ($value->skill) {
                    $arrayKey = array_search($languageCode, array_column($value->skill->translations, 'lang'));
                    if ($arrayKey  !== '') {
                        $returnData[config('constants.SKILL')][$key]['title'] =
                        $value->skill->translations[$arrayKey]['title'];
                        $returnData[config('constants.SKILL')][$key]['id'] =
                        $value->skill->skill_id;
                        $returnData[config('constants.SKILL')][$key]['mission_count'] =
                        $value->mission_count;
                    }
                }
                if (isset($returnData[config('constants.SKILL')])) {
                    $apiData[config('constants.SKILL')] = $returnData[config('constants.SKILL')];
                }
            }
        }

        if (!empty($missionState->toArray())) {
            foreach ($missionState as $key => $value) {
                if (isset($value->state)) {
                    $translation = $value->state->languages->toArray();
                    $translationkey = '';

                    $index = array_search($languageId, array_column($translation, 'language_id'));
                    $language = ($index === false) ? $defaultLanguageId : $languageId;
                    $translationkey = array_search($language, array_column($translation, 'language_id'));

                    if ($translationkey !== '') {
                        $returnData[config('constants.STATE')]['title'] =
                        $translation[$translationkey]['name'];
                    }
                    $returnData[config('constants.STATE')]['id'] =
                    $value->state_id;
                    $returnData[config('constants.STATE')]['mission_count'] =
                    $value->mission_count;
                }
                if (isset($returnData[config('constants.STATE')])) {
                    $apiData[config('constants.STATE')] = isset($apiData[config('constants.STATE')]) ?
                    $apiData[config('constants.STATE')] : [];
                    array_push($apiData[config('constants.STATE')], $returnData[config('constants.STATE')]);                 
                }
            }
        }
        
        $apiStatus = Response::HTTP_OK;
        return $this->responseHelper->success(
            $apiStatus,
            '',
            $apiData
        );
    }

    /**
     * Add/remove mission to favourite
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missionFavourite(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    "mission_id" => "numeric",
                ]
            );
    
            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_INVALID_MISSION_ID'),
                    $validator->errors()->first()
                );
            }
            $missionId = $request->mission_id;
            $missionFavourite = $this->missionRepository->missionFavourite($request->auth->user_id, $missionId);

            // Set response data
            $apiData = ($missionFavourite !== null)
            ? ['favourite_mission_id' => $missionFavourite->favourite_mission_id] : [];
            $apiStatus = ($missionFavourite !== null) ? Response::HTTP_CREATED
            : Response::HTTP_OK;
            $apiMessage = ($missionFavourite !== null) ?
            trans('messages.success.MESSAGE_MISSION_ADDED_TO_FAVOURITE') :
            trans('messages.success.MESSAGE_MISSION_DELETED_FROM_FAVOURITE');
            
            // Make activity log
            $favouriteStatus = ($missionFavourite != null) ?
            config('constants.activity_log_actions.ADD_TO_FAVOURITE'):
            config('constants.activity_log_actions.REMOVE_FROM_FAVOURITE');
            
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.MISSION'),
                $favouriteStatus,
                config('constants.activity_log_user_types.REGULAR'),
                $request->auth->email,
                get_class($this),
                $request->toArray(),
                $request->auth->user_id,
                $request->mission_id
            ));
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
    }
    
    /**
     * Get Mission Filter Tags
     *
     * @param Illuminate\Http\Request $request
     * @param object $language
     * @param App\Models\UserFilter $userFilters
     * @return Array
     */
    public function missionFiltersTag(Request $request, object $language, UserFilter $userFilters): array
    {
        $language = $this->languageHelper->getLanguageDetails($request);
        $languageId = $language->language_id;

        // Get data of user's filter
        $filterTagArray = [];
        $filterData= $userFilters->toArray();

        if (!empty($filterData["filters"])) {
            if ($filterData["filters"]["country_id"] && $filterData["filters"]["country_id"] !== "") {
                $countryTag = $this->countryRepository->getCountry(
                    $filterData["filters"]["country_id"],
                    $languageId
                );
                if ($countryTag["name"]) {
                    $filterTagArray["country"][$countryTag["country_id"]] = $countryTag["name"];
                }
            }

            if ($filterData["filters"]["state_id"] && $filterData["filters"]["state_id"] !== "") {
                $stateTag = $this->stateRepository->getState(
                    $filterData["filters"]["state_id"],
                    $languageId
                );
                if ($stateTag) {
                    foreach ($stateTag as $key => $value) {
                        $filterTagArray["state"][$key] = $value;
                    }
                }
            }

            if ($filterData["filters"]["city_id"] && $filterData["filters"]["city_id"] !== "") {
                $cityTag = $this->cityRepository->getCity(
                    $filterData["filters"]["city_id"],
                    $languageId
                );
                if ($cityTag) {
                    foreach ($cityTag as $key => $value) {
                        $filterTagArray["city"][$key] = $value;
                    }
                }
            }
            
            if ($filterData["filters"]["theme_id"] && $filterData["filters"]["theme_id"] !== "") {
                $themeTag = $this->themeRepository->missionThemeList($request, $filterData["filters"]["theme_id"]);
                if ($themeTag) {
                    foreach ($themeTag as $value) {
                        if ($value->translations) {
                            $arrayKey = array_search($language->code, array_column($value->translations, 'lang'));
                            if ($arrayKey  !== '') {
                                $filterTagArray["theme"][$value->mission_theme_id] =
                                $value->translations[$arrayKey]['title'];
                            }
                        }
                    }
                }
            }

            if ($filterData["filters"]["skill_id"] && $filterData["filters"]["skill_id"] !== "") {
                $skillTag = $this->skillRepository->skillList($request, $filterData["filters"]["skill_id"]);
                if ($skillTag) {
                    foreach ($skillTag as $value) {
                        if ($value->translations) {
                            $arrayKey = array_search($language->code, array_column($value->translations, 'lang'));
                            if ($arrayKey  !== '') {
                                $filterTagArray["skill"][$value->skill_id] =
                                $value->translations[$arrayKey]['title'];
                            }
                        }
                    }
                }
            }
        }

        return $filterTagArray;
    }

    /**
     * Get related missions listing
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function getRelatedMissions(Request $request, int $missionId): JsonResponse
    {
        try {
            $language = $this->languageHelper->getLanguageDetails($request);
            $languageId = $language->language_id;

            $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
            $defaultTenantLanguageId = $defaultTenantLanguage->language_id;
            $missionData = $this->missionRepository->getRelatedMissions($request, $missionId);
            $timezone = $this->userRepository->getUserTimezone($request->auth->user_id);
            $mission = $missionData->map(function (Mission $mission) use (
                $languageId,
                $defaultTenantLanguageId,
                $timezone
            ) {
                return $this->transformMission($mission, '', $languageId, $defaultTenantLanguageId, $timezone);
            })->all();
           
            $apiData = $mission;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (!empty($mission)) ?
            trans('messages.success.MESSAGE_MISSION_LISTING') :
            trans('messages.success.MESSAGE_NO_RELATED_MISSION_FOUND');
            return $this->responseHelper->success(
                $apiStatus,
                $apiMessage,
                $apiData
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
    }
    
    /**
     * Get missions detail
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function getMissionDetail(Request $request, int $missionId): JsonResponse
    {
        try {
            $language = $this->languageHelper->getLanguageDetails($request);
            $languageId = $language->language_id;
            $languageCode = $language->code;

            $missionData = $this->missionRepository->getMissionDetail($request, $missionId);
            $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
            $defaultTenantLanguageId = $defaultTenantLanguage->language_id;
            $timezone = $this->userRepository->getUserTimezone($request->auth->user_id);

            $mission = $missionData->map(
                function (Mission $mission) use ($languageCode, $languageId, $defaultTenantLanguageId, $timezone
                ) {
                    return $this->transformMission(
                        $mission,
                        $languageCode,
                        $languageId,
                        $defaultTenantLanguageId,
                        $timezone
                    );
                }
            )->all();

            $apiData = $mission;
            $apiStatus = (empty($mission)) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;
            $apiMessage = (empty($mission)) ? trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND') :
             trans('messages.success.MESSAGE_MISSION_FOUND');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        }
    }

    /**
     * Get user mission lists
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getUserMissions(Request $request): JsonResponse
    {
        $missionLists = $this->missionRepository->getUserMissions($request);

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiData = $missionLists;
        $apiMessage = (empty($apiData)) ? trans('messages.custom_error_message.ERROR_USER_MISSIONS_NOT_FOUND')
        : trans('messages.success.MESSAGE_MISSION_LISTING');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
