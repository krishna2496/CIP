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

    /**
     * Create a new Mission controller instance.
     *
     * @param App\Repositories\Mission\MissionRepository $missionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MissionRepository $missionRepository,
        ResponseHelper $responseHelper,
        UserFilterRepository $userFilterRepository
    ) {
        $this->missionRepository = $missionRepository;
        $this->responseHelper = $responseHelper;
        $this->userFilterRepository = $userFilterRepository;
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
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.'.config('constants.error_codes.ERROR_DATABASE_OPERATIONAL')
                )
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_INVALID_ARGUMENT'))
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
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
            $languages = LanguageHelper::getLanguages($request);
            $local = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $language = $languages->where('code', $local)->first();
            $languageId = $language->language_id;

            //Save User search data
            $this->userFilterRepository->saveFilter($request);

            // Get users filter
            $userFilters = $this->userFilterRepository->userFilter($request);
            $userFilterData = $userFilters->toArray()["filters"];

            // Get Data by top theme
            $topTheme = $this->missionRepository->topMission($request, config('constants.TOP_THEME'));
            // Get Data by top country
            $topCountry = $this->missionRepository->topMission($request, config('constants.TOP_COUNTRY'));

            $topMissionData = Helpers::missionTopData(
                $topTheme,
                $topCountry,
                $local
            );
           
            $mission = $this->missionRepository->appMissions($request, $userFilterData, $languageId);
            foreach ($mission as $key => $value) {
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
            $metaData[config('constants.TOP_THEME')] = $topMissionData[config('constants.TOP_THEME')];
            $metaData[config('constants.TOP_COUNTRY')] = $topMissionData[config('constants.TOP_COUNTRY')];
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
            dd($e);
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MISSION_NOT_FOUND'),
                trans('messages.custom_error_message.'.config('constants.error_codes.ERROR_MISSION_NOT_FOUND'))
            );
        } catch (PDOException $e) {
            dd($e);
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.'.config('constants.error_codes.ERROR_DATABASE_OPERATIONAL')
                )
            );
        } catch (\Exception $e) {
            dd($e);
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }
}
