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
        CityRepository $cityRepository
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
    }

    /**
     * Get missions listing
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getMissionList(Request $request): JsonResponse
    {
        $languages = $this->languageHelper->getLanguages($request);
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $language = $languages->where('code', $language)->first();
        $languageId = $language->language_id;
        $languageCode = $language->code;

        //Save User search data
        $this->userFilterRepository->saveFilter($request);
        // Get users filter
        $userFilters = $this->userFilterRepository->userFilter($request);
        $filterTagArray = $this->missionFiltersTag($request, $language, $userFilters);
        $userFilterData = $userFilters->toArray()["filters"];
        // Checking explore mission type is out of list or not
        if ($request->has('explore_mission_type') && $request->input('explore_mission_type') != '') {
            $exploreMissionType = $request->input('explore_mission_type');
            if ($exploreMissionType != config('constants.TOP_RECOMMENDED') &&
                $exploreMissionType != config('constants.RANDOM') &&
                $exploreMissionType !=config('constants.THEME') &&
                $exploreMissionType != config('constants.COUNTRY') &&
                $exploreMissionType != config('constants.ORGANIZATION') &&
                $exploreMissionType != config('constants.ORGANIZATION') &&
                $exploreMissionType != config('constants.MOST_RANKED') &&
                $exploreMissionType != config('constants.TOP_FAVOURITE')
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


        $missionList = $this->missionRepository->getMissions($request, $userFilterData, $languageId);

        $missionsTransformed = $missionList
            ->getCollection()
            ->map(function ($item) use ($languageCode) {
                return $this->transformMission($item, $languageCode);
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
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        // Get Data by top theme
        $topTheme = $this->missionRepository->exploreMission($request, config('constants.TOP_THEME'));
        // Get Data by top country
        $topCountry = $this->missionRepository->exploreMission($request, config('constants.TOP_COUNTRY'));
        // Get Data by top organization
        $topOrganisation = $this->missionRepository->exploreMission($request, config('constants.TOP_ORGANISATION'));

        if (!empty($topTheme->toArray())) {
            foreach ($topTheme as $key => $value) {
                if ($value->missionTheme && $value->missionTheme->translations) {
                    $arrayKey = array_search($language, array_column($value->missionTheme->translations, 'lang'));
            
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
        if (!empty($topCountry->toArray())) {
            foreach ($topCountry as $key => $value) {
                if ($value->country) {
                    $returnData[config('constants.TOP_COUNTRY')][$key]['title'] =
                    $value->country->name;
                    $returnData[config('constants.TOP_COUNTRY')][$key]['id'] =
                    $value->country->country_id;
                }
            }
            $apiData[config('constants.TOP_COUNTRY')] = $returnData[config('constants.TOP_COUNTRY')];
        }
        if (!empty($topOrganisation->toArray())) {
            foreach ($topOrganisation as $key => $value) {
                if ($value->organisation_name != '') {
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
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        // Get Data by country
        $missionCountry = $this->missionRepository->missionFilter($request, config('constants.COUNTRY'));
        // Get Data by top theme
        $missionCity = $this->missionRepository->missionFilter($request, config('constants.CITY'));
        // Get Data by top organization
        $missionTheme = $this->missionRepository->missionFilter($request, config('constants.THEME'));
        // Get Data by skills
        $missionSkill = $this->missionRepository->missionFilter($request, config('constants.SKILL'));
    
        if (!empty($missionCountry->toArray())) {
            foreach ($missionCountry as $key => $value) {
                if ($value->country) {
                    $returnData[config('constants.COUNTRY')][$key]['title'] =
                    $value->country->name;
                    $returnData[config('constants.COUNTRY')][$key]['id'] =
                    $value->country->country_id;
                    $returnData[config('constants.COUNTRY')][$key]['mission_count'] =
                    $value->mission_count;
                }
                if (isset($returnData[config('constants.COUNTRY')])) {
                    $apiData[config('constants.COUNTRY')] = $returnData[config('constants.COUNTRY')];
                }
            }
        }


        if (!empty($missionCity->toArray())) {
            foreach ($missionCity as $key => $value) {
                $returnData[config('constants.CITY')][$key]['title'] =
                    $value->city_name;
                $returnData[config('constants.CITY')][$key]['id'] =
                    $value->city_id;
                $returnData[config('constants.CITY')][$key]['mission_count'] =
                    $value->mission_count;
            }
            if (isset($returnData[config('constants.CITY')])) {
                $apiData[config('constants.CITY')] = $returnData[config('constants.CITY')];
            }
        }
        
        if (!empty($missionTheme->toArray())) {
            foreach ($missionTheme as $key => $value) {
                if ($value->missionTheme && $value->missionTheme->translations) {
                    $arrayKey = array_search($language, array_column($value->missionTheme->translations, 'lang'));
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
                    $arrayKey = array_search($language, array_column($value->skill->translations, 'lang'));
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
            $missionFavourite = $this->missionRepository
            ->missionFavourite($request->auth->user_id, $missionId);

            // Set response data
            $apiData = ($missionFavourite != null)
            ? ['favourite_mission_id' => $missionFavourite->favourite_mission_id] : [];
            $apiStatus = ($missionFavourite != null) ? Response::HTTP_CREATED
            : Response::HTTP_OK;
            $apiMessage = ($missionFavourite != null) ?
            trans('messages.success.MESSAGE_MISSION_ADDED_TO_FAVOURITE') :
            trans('messages.success.MESSAGE_MISSION_DELETED_FROM_FAVOURITE');
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
        // Get data of user's filter
        $filterTagArray = [];
        $filterData= $userFilters->toArray();

        if (!empty($filterData["filters"])) {
            if ($filterData["filters"]["country_id"] && $filterData["filters"]["country_id"] != "") {
                $countryTag = $this->countryRepository->getCountry($filterData["filters"]["country_id"]);
                if ($countryTag["name"]) {
                    $filterTagArray["country"][$countryTag["country_id"]] = $countryTag["name"];
                }
            }

            if ($filterData["filters"]["city_id"] && $filterData["filters"]["city_id"] != "") {
                $cityTag = $this->cityRepository->getCity($filterData["filters"]["city_id"]);
                if ($cityTag) {
                    foreach ($cityTag as $key => $value) {
                        $filterTagArray["city"][$key] = $value;
                    }
                }
            }
            
            if ($filterData["filters"]["theme_id"] && $filterData["filters"]["theme_id"] != "") {
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

            if ($filterData["filters"]["skill_id"] && $filterData["filters"]["skill_id"] != "") {
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
            $languages = $this->languageHelper->getLanguages($request);
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $language = $languages->where('code', $language)->first();
            $languageId = $language->language_id;

            $missionData = $this->missionRepository->getRelatedMissions($request, $languageId, $missionId);
            $mission = $missionData->map(function (Mission $mission) {
                return $this->transformMission($mission, '');
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
            $languages = $this->languageHelper->getLanguages($request);
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $language = $languages->where('code', $language)->first();
            $languageId = $language->language_id;
            $languageCode = $language->code;

            $missionData = $this->missionRepository->getMissionDetail($request, $languageId, $missionId);
            $mission = $missionData->map(function (Mission $mission) use ($languageCode) {
                return $this->transformMission($mission, $languageCode);
            })->all();

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
        try {
            $missionLists = $this->missionRepository->getUserMissions($request);
   
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiData = $missionLists;
            $apiMessage = (empty($apiData)) ? trans('messages.custom_error_message.ERROR_USER_MISSIONS_NOT_FOUND')
            : trans('messages.success.MESSAGE_MISSION_LISTING');

            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
