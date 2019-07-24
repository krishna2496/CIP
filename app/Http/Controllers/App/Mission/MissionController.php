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
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use InvalidArgumentException;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;
use App\Models\UserFilter;

class MissionController extends Controller
{
    use RestExceptionHandlerTrait;
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

    /*
     * @var App\Helpers\LanguageHelper
     */

    private $languageHelper;

    /*
     * @var App\Helpers\Helpers
     */

    private $helpers;

    /*
     * @var App\Repositories\MissionTheme\MissionThemeRepository;
     */

    private $theme;

    /*
     * @var App\Repositories\Skill\SkillRepository
     */

    private $skill;

    /**
     * Create a new Mission controller instance
     *
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param Illuminate\Http\UserFilterRepository $userFilterRepository
     * @param  Illuminate\Http\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\Helpers $theme
     * @param App\Helpers\Helpers $skill
     * @return void
     */
    public function __construct(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        UserFilterRepository $userFilterRepository,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        MissionThemeRepository $theme,
        SkillRepository $skill
    ) {
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
        $this->userFilterRepository = $userFilterRepository;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->theme = $theme;
        $this->skill = $skill;
    }

    /**
     * Get missions listing
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function missionList(Request $request): JsonResponse
    {
        try {
            $missions = $this->missionRepository->missionDetail($request);
            
            $apiData = $missions;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
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
     * Get missions listing
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function appMissionList(Request $request): JsonResponse
    {
        try {
            $languages = $this->languageHelper->getLanguages($request);
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $language = $languages->where('code', $language)->first();
            $languageId = $language->language_id;
            //Save User search data
            $this->userFilterRepository->saveFilter($request);
            // Get users filter
            $userFilters = $this->userFilterRepository->userFilter($request);

            $filterTagArray = $this->missionFiltersTag($request, $language, $userFilters);
            
            $userFilterData = $userFilters->toArray()["filters"];
           
            $mission = $this->missionRepository->appMissions($request, $userFilterData, $languageId);

            foreach ($mission as $key => $value) {
                if (isset($value->goalMission)) {
                    $value->goal_objective  = $value->goalMission->goal_objective;
                    unset($value->goalMission);
                }

                if (isset($value->timeMission)) {
                    $value->application_deadline = $value->timeMission->application_deadline;
                    $value->application_start_date = $value->timeMission->application_start_date;
                    $value->application_end_date = $value->timeMission->application_end_date;
                    $value->application_start_time = $value->timeMission->application_start_time;
                    $value->application_end_time = $value->timeMission->application_end_time;

                    unset($value->timeMission);
                    unset($value->goalMission);
                }

                unset($value->city);
                if ($value->mission_type == config("constants.MISSION_TYPE['GOAL']")) {
                    //Progress bar for goal
                }
    
                if ($value->total_seats != 0 && $value->total_seats !== null) { //With limited seats
                    $value->seats_left = ($value->total_seats) - ($value->mission_application_count);
                } else { //Unlimeted seats
                    $value->already_volunteered = $value->mission_application_count;
                }
    
                // Get defalut media image
                $value->default_media_type = $value->missionMedia[0]->media_type ?? '';
                $value->default_media_path = $value->missionMedia[0]->media_path ?? '';
                unset($value->missionMedia);
    
                // Set title and description
                $value->title = $value->missionLanguage[0]->title ?? '';
                $value->short_description = $value->missionLanguage[0]->short_description ?? '';
                $value->objective = $value->missionLanguage[0]->objective ?? '';
                unset($value->missionLanguage);
    
                // Check for apply in mission validity
                $value->set_view_detail = 0;
                $today = $this->helpers->getUserTimeZoneDate(date(config("constants.DB_DATE_FORMAT")));
                
                if (($value->user_application_count > 0) ||
                    ($value->application_deadline !== null && $value->application_deadline < $today) ||
                    ($value->total_seats != 0 && $value->total_seats == $value->mission_application_count) ||
                    ($value->end_date !== null && $value->end_date < $today)
                    // || ($value->mission_type != 'GOAL' && $value->goal_objective ==  $today)
                ) {
                    $value->set_view_detail = 1;
                }
                $value->mission_rating_count = $value->mission_rating_count ?? 0;
            }
            $metaData['filters'] = $userFilterData;
            $metaData['filters']["tags"] = $filterTagArray;
            $apiData = $mission;
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LISTING');
            return $this->responseHelper->successWithPagination(
                $apiStatus,
                $apiMessage,
                $apiData,
                $metaData
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get explore mission data
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function exploreMission(Request $request): JsonResponse
    {
        try {
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
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get filter mission data
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function filters(Request $request): JsonResponse
    {
        try {
            $returnData = [];
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
                }
                $apiData[config('constants.COUNTRY')] = $returnData[config('constants.COUNTRY')];
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
                $apiData[config('constants.CITY')] = $returnData[config('constants.CITY')];
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
                }
                $apiData[config('constants.THEME')] = $returnData[config('constants.THEME')];
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
                }

                $apiData[config('constants.SKILL')] = $returnData[config('constants.SKILL')];
            }
            
            $apiStatus = Response::HTTP_OK;
            return $this->responseHelper->success(
                $apiStatus,
                '',
                $apiData
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
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
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
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
        try {
            // Get data of user's filter
            $filterTagArray = [];
            $filterData= $userFilters->toArray();

            if (!empty($filterData["filters"])) {
                if ($filterData["filters"]["country_id"] && $filterData["filters"]["country_id"] != "") {
                    $countryTag = $this->helpers->getCountry($filterData["filters"]["country_id"]);
                    if ($countryTag["name"]) {
                        $filterTagArray["country"][$countryTag["country_id"]] = $countryTag["name"];
                    }
                }

                if ($filterData["filters"]["city_id"] && $filterData["filters"]["city_id"] != "") {
                    $cityTag = $this->helpers->getCity($filterData["filters"]["city_id"]);
                    if ($cityTag) {
                        foreach ($cityTag as $key => $value) {
                            $filterTagArray["city"][$key] = $value;
                        }
                    }
                }

                if ($filterData["filters"]["theme_id"] && $filterData["filters"]["theme_id"] != "") {
                    $themeTag = $this->theme->missionThemeList($request, $filterData["filters"]["theme_id"]);
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
                    $skillTag = $this->skill->skillList($request, $filterData["filters"]["skill_id"]);
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
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    /**
     * Get missions detail
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Http\JsonResponse
     */
    public function missionDetail(Request $request, int $missionId): JsonResponse
    {
        try {
            $languages = $this->languageHelper->getLanguages($request);
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $language = $languages->where('code', $language)->first();
            $languageId = $language->language_id;

            $missionData = $this->missionRepository->missionDetail($request, $languageId, $missionId);
            $mission = $missionData->toArray();

            if (isset($mission['goal_mission'])) {
                $mission['goal_objective']  = $mission['goal_mission']['goal_objective'];
            }
            if (isset($mission['time_mission'])) {
                $mission['application_deadline'] = $mission['time_mission']['application_deadline'];
                $mission['application_start_date'] = $mission['time_mission']['application_start_date'];
                $mission['application_end_date'] = $mission['time_mission']['application_end_date'];
                $mission['application_start_time'] = $mission['time_mission']['application_start_time'];
                $mission['application_end_time'] = $mission['time_mission']['application_end_time'];
            }
            unset($mission['goal_mission']);
            unset($mission['time_mission']);

            $mission['user_application_status']  = ($mission['mission_application'][0]['approval_status']) ?? '';
            $mission['mission_rating']  = ($mission['mission_rating'][0]['rating']) ?? 0;
            $mission['is_favourite']  = (empty($mission['favourite_mission'])) ? 0 : 1;
            unset($mission['mission_rating']);
            unset($mission['favourite_mission']);
            unset($mission['mission_application']);
           
            // Set seats_left or already_volunteered
            if ($mission['total_seats'] != 0 && $mission['total_seats'] !== null) {
                $mission['seats_left'] = ($mission['total_seats']) - ($mission['mission_application_count']);
            } else {
                $mission['already_volunteered'] = $mission['mission_application_count'];
            }
    
            // Get defalut media image
            $mission['default_media_type'] = $mission['mission_media'][0]['media_type'] ?? '';
            $mission['default_media_path'] = $mission['mission_media'][0]['media_path'] ?? '';
            unset($mission['mission_media']);
            unset($mission['city']);
    
            // Set title and description
            $mission['title'] = $mission['mission_language'][0]['title'] ?? '';
            $mission['short_description'] = $mission['mission_language'][0]['short_description'] ?? '';
            $mission['objective'] = $mission['mission_language'][0]['objective'] ?? '';
            unset($mission['mission_language']);
    
            // Check for apply in mission validity
            $mission['set_view_detail'] = 0;
            $today = $this->helpers->getUserTimeZoneDate(date(config("constants.DB_DATE_FORMAT")));
                
            if (($mission['user_application_count'] > 0) ||
                    (isset($mission['application_deadline']) && $mission['application_deadline'] < $today) ||
                    ($mission['total_seats'] != 0
                    && $mission['total_seats'] == $mission['mission_application_count']) ||
                    ($mission['end_date'] !== null && $mission['end_date'] < $today)
                ) {
                $mission['set_view_detail'] = 1;
            }
            $mission['mission_rating_count'] = $mission['mission_rating_count'] ?? 0;
            $apiData = $mission;
            if (!empty($mission['mission_skill'])) {
                foreach ($mission['mission_skill'] as $key => $value) {
                    if ($value['skill']) {
                        $arrayKey = array_search($language->code, array_column(
                            $value['skill']['translations'],
                            'lang'
                        ));
                        if ($arrayKey  !== '') {
                            $returnData[config('constants.SKILL')][$key]['title'] =
                            $value['skill']['translations'][$arrayKey]['title'];
                            $returnData[config('constants.SKILL')][$key]['id'] =
                            $value['skill']['skill_id'];
                        }
                    }
                }
                $apiData[config('constants.SKILL')] = $returnData[config('constants.SKILL')];
            }
            unset($apiData['mission_skill']);
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MISSION_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
