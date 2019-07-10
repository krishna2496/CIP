<?php

namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Mission\MissionRepository;
use App\Repositories\UserFilter\UserFilterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Mission;
use App\Models\MissionLanguage;
use App\Models\MissionDocument;
use App\Models\MissionMedia;
use App\Models\MissionTheme;
use App\Models\MissionRating;
use App\Models\MissionApplication;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use InvalidArgumentException;
use PDOException;
use Illuminate\Http\JsonResponse;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

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

    /**
     * Create a new Mission controller instance.
     *
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param Illuminate\Http\UserFilterRepository $userFilterRepository
     * @param  Illuminate\Http\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        UserFilterRepository $userFilterRepository,
        LanguageHelper $languageHelper
    ) {
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
        $this->userFilterRepository = $userFilterRepository;
        $this->languageHelper = $languageHelper;
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
    
                if ($value->total_seats != 0) { //With limited seats
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
                $today = date(config("constants.DATE_FORMAT"));
    
                if (($value->user_application_count > 0) ||
                    ($value->application_deadline !== null && $value->application_deadline < $today) ||
                    ($value->total_seats != 0 && $value->total_seats == $value->mission_application_count) ||
                    ($value->end_date !== null && $value->end_date < $today)
                    // || ($value->mission_type != 'GOAL' && $value->goal_objective ==  $today)
                ) {
                    $value->set_view_detail = 1;
                }
            }
            $metaData['filters'] = $userFilterData;
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
                        }
                    }
                }
                $apiData[config('constants.THEME')] = $returnData[config('constants.THEME')];
            }

            if (!empty($missionSkill->toArray())) {
                $missionSkill = $missionSkill->toArray();
                foreach ($missionSkill as $key => $value) {
                    $missionSkillArray = $value['mission_skill'];
                    if (!empty($missionSkillArray)) {
                        foreach ($missionSkillArray as $missionSkillKey => $missionSkillValue) {
                            if ($missionSkillValue['skill']['translations']) {
                                $arrayKey = array_search($language, array_column(
                                    $missionSkillValue['skill']['translations'],
                                    'lang'
                                ));
                              
                                if ($arrayKey  !== '') {
                                    $returnData[config('constants.SKILL')][$missionSkillValue['skill']['skill_id']]
                                    ['title']=$missionSkillValue['skill']['translations'][$arrayKey]['title'];
                                    $returnData[config('constants.SKILL')][$missionSkillValue['skill']['skill_id']]
                                    ['id']= $missionSkillValue['skill']['skill_id'];
                                }
                            }
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
     * Add/remove mission to favourite.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function missionFavourite(Request $request): JsonResponse
    {
        try {
            $missionFavourite = $this->missionRepository
            ->missionFavourite($request->auth->user_id, $request->toArray());

            // Set response data
            $apiData = ($missionFavourite != null)
            ? ['favourite_mission_id' => $missionFavourite->favourite_mission_id] : [];
            $apiStatus = Response::HTTP_OK;
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
            
    /*
     * Get mission ratings
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function missionRatings(int $id): JsonResponse
    {
        try {
            $rating = $this->missionRepository->missionRatings($id);
            
            $apiData = ['rating' => $rating ];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_MISSION_RATING_LISTING');
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
}
