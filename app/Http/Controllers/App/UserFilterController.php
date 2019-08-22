<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\UserFilter\UserFilterRepository;
use App\Repositories\Skill\SkillRepository;
use App\Repositories\MissionTheme\MissionThemeRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\City\CityRepository;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use PDOException;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;

class UserFilterController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\UserFilter\UserFilterRepository
     */
    private $filters;

    /**
     * @var App\Repositories\MissionTheme\MissionThemeRepository
     */
    private $theme;

    /**
     * @var App\Repositories\Skill\SkillRepository
     */
    private $skill;

    /**
     * @var App\Repositories\Skill\SkillRepository
     */
    private $helper;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;

    /**
     * @var App\Repositories\City\CityRepository
     */
    private $cityRepository;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\UserFilter\UserFilterRepository $filters
     * @param App\Repositories\MissionTheme\MissionThemeRepository $theme
     * @param App\Repositories\Skill\SkillRepository $skill
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helper
     * @param App\Repositories\Country\CountryRepository $countryRepository
     * @param App\Repositories\City\CityRepository $cityRepository
     * @return void
     */
    public function __construct(
        UserFilterRepository $filters,
        MissionThemeRepository $theme,
        SkillRepository $skill,
        ResponseHelper $responseHelper,
        Helpers $helper,
        CountryRepository $countryRepository,
        CityRepository $cityRepository
    ) {
        $this->filters = $filters;
        $this->responseHelper = $responseHelper;
        $this->theme = $theme;
        $this->skill = $skill;
        $this->helper = $helper;
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
    }
    
    /**
     * Display listing of user filter
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request):JsonResponse
    {
        try {
            // Get data of user's filter
            $filterTagArray = [];
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $filters = $this->filters->userFilter($request);
            $filterData = $filters->toArray();

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
                    $themeTag = $this->theme->missionThemeList($request, $filterData["filters"]["theme_id"]);
                    
                    if ($themeTag) {
                        foreach ($themeTag as $value) {
                            if ($value->translations) {
                                $arrayKey = array_search($language, array_column($value->translations, 'lang'));
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
                                $arrayKey = array_search($language, array_column($value->translations, 'lang'));
                                if ($arrayKey  !== '') {
                                    $filterTagArray["skill"][$value->skill_id] =
                                    $value->translations[$arrayKey]['title'];
                                }
                            }
                        }
                    }
                }
            }
            
            $filterData["filters"]["tags"] = $filterTagArray;
            $apiStatus = Response::HTTP_OK;
            return $this->responseHelper->success($apiStatus, '', $filterData);
        } catch (\PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
