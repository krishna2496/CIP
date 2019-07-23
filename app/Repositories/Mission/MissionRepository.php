<?php
namespace App\Repositories\Mission;

use App\Repositories\Mission\MissionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Models\Mission;
use App\Models\MissionLanguage;
use App\Models\MissionDocument;
use App\Models\MissionMedia;
use App\Models\FavouriteMission;
use App\Models\MissionSkill;
use App\Models\TimeMission;
use App\Models\MissionRating;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class MissionRepository implements MissionInterface
{
    /**
     * @var App\Models\Mission
     */
    public $mission;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /*
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * @var App\Models\FavouriteMission
     */
    public $favouriteMission;

    /**
     * @var App\models\MissionSkill
     */
    private $missionSkill;

    /**
     * @var App\models\MissionRating
     */
    private $missionRating;

    /**
     * Create a new Mission repository instance.
     *
     * @param  App\Models\Mission $mission
     * @param  App\Models\MissionLanguage $missionLanguage
     * @param  App\Models\MissionMedia $missionMedia
     * @param  App\Models\MissionDocument $missionDocument
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  Illuminate\Http\LanguageHelper $languageHelper
     * @param  Illuminate\Http\S3Helper $s3helper
     * @param App\Models\MissionSkill
     * @param  App\Models\FavouriteMission $favouriteMission
     * @param  App\Models\MissionRating $missionRating
     * @return void
     */
    public function __construct(
        Mission $mission,
        MissionLanguage $missionLanguage,
        MissionMedia $missionMedia,
        MissionDocument $missionDocument,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        FavouriteMission $favouriteMission,
        MissionSkill $missionSkill,
        MissionRating $missionRating
    ) {
        $this->mission = $mission;
        $this->missionLanguage = $missionLanguage;
        $this->missionMedia = $missionMedia;
        $this->missionDocument = $missionDocument;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
        $this->favouriteMission = $favouriteMission;
        $this->missionSkill = $missionSkill;
        $this->missionRating = $missionRating;
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Mission
     */
    public function store(Request $request): Mission
    {
        $languages = $this->languageHelper->getLanguages($request);
        $countryId = $this->helpers->getCountryId($request->location['country_code']);
        $missionData = array(
                'theme_id' => $request->theme_id,
                'city_id' => $request->location['city_id'],
                'country_id' => $countryId,
                'start_date' => (isset($request->start_date)) ? $request->start_date : null,
                'end_date' => (isset($request->end_date)) ? $request->end_date : null,
                'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ?
                 $request->total_seats : null,
                'publication_status' => $request->publication_status,
                'organisation_id' => $request->organisation['organisation_id'],
                'organisation_name' => $request->organisation['organisation_name'],
                'mission_type' => $request->mission_type
            );
        
        // Create new record
        $mission = $this->mission->create($missionData);

        // Entry into goal_mission table
        if ($request->mission_type == config('constants.mission_type.GOAL')) {
            $goalMissionArray = array(
                'goal_objective' => $request->goal_objective
            );
            $mission->goalMission()->create($goalMissionArray);
        }

        // Entry into time_mission table
        if ($request->mission_type == "TIME") {
            $timeMissionArray = array(
                'application_deadline' => (isset($request->application_deadline)) ?
                $request->application_deadline : null,
                'application_start_date' => (isset($request->application_start_date))
                ? $request->application_start_date : null,
                'application_end_date' => (isset($request->application_end_date))
                ? $request->application_end_date : null,
                'application_start_time' => (isset($request->application_start_time))
                ? $request->application_start_time : null,
                'application_end_time' => (isset($request->application_end_time))
                ? $request->application_end_time : null,
            );

            $mission->timeMission()->create($timeMissionArray);
        }
        // Add mission title
        foreach ($request->mission_detail as $value) {
            $language = $languages->where('code', $value['lang'])->first();
            $missionLanguage = array(
                    'mission_id' => $mission->mission_id,
                    'language_id' => $language->language_id,
                    'title' => $value['title'],
                    'short_description' => (isset($value['short_description'])) ? $value['short_description'] : null,
                    'description' => (array_key_exists('section', $value)) ? $value['section'] : '',
                    'objective' => $value['objective']
                );

            $this->missionLanguage->create($missionLanguage);
            unset($missionLanguage);
        }
        
        // For skills
        if (isset($request->skills) && count($request->skills) > 0) {
            foreach ($request->skills as $value) {
                $this->missionSkill->linkMissionSkill($mission->mission_id, $value['skill_id']);
            }
        }
        
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $isDefault = 0;

        // Add mission media images
        if (isset($request->media_images) && count($request->media_images) > 0) {
            foreach ($request->media_images as $value) {
                $filePath = $this->s3helper->uploadFileOnS3Bucket($value['media_path'], $tenantName);
                // Check for default image in mission_media
                $default = (isset($value['default']) && ($value['default'] != '')) ? $value['default'] : '0';
                if ($default == '1') {
                    $isDefault = 1;
                    $media = array('default' => '0');
                    $this->missionMedia->where('mission_id', $mission->mission_id)->update($media);
                }
                
                $missionMedia = array(
                        'mission_id' => $mission->mission_id,
                        'media_name' => $value['media_name'],
                        'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                        'media_path' => $filePath,
                        'default' => $default
                    );
                $this->missionMedia->create($missionMedia);
                unset($missionMedia);
            }

            if ($isDefault == 0) {
                $mediaData = $this->missionMedia->where('mission_id', $mission->mission_id)
                ->orderBy('mission_media_id', 'ASC')->first();
                $missionMedia = array('default' => '1');
                $this->missionMedia->where('mission_media_id', $mediaData->mission_media_id)->update($missionMedia);
            }
        }
        // Add mission media videos
        if (isset($request->media_videos) && count($request->media_videos) > 0) {
            if (!empty($request->media_videos)) {
                foreach ($request->media_videos as $value) {
                    $missionMedia = array('mission_id' => $mission->mission_id,
                                          'media_name' => $value['media_name'],
                                          'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                          'media_path' => $value['media_path']);
                    $this->missionMedia->create($missionMedia);
                    unset($missionMedia);
                }
            }
        }
            
        // Add mission documents
        if (isset($request->documents) && count($request->documents) > 0) {
            if (!empty($request->documents)) {
                foreach ($request->documents as $value) {
                    $filePath = $this->s3helper->uploadFileOnS3Bucket($value['document_path'], $tenantName);
                    $missionDocument = array('mission_id' => $mission->mission_id,
                                            'document_name' => $value['document_name'],
                                            'document_type' => pathinfo($value['document_name'], PATHINFO_EXTENSION),
                                            'document_path' => $filePath);
                    $this->missionDocument->create($missionDocument);
                    unset($missionDocument);
                }
            }
        }
        return $mission;
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return App\Models\Mission
     */
    public function update(Request $request, int $id): Mission
    {
        $languages = $this->languageHelper->getLanguages($request);
        // Set data for update record
        if (isset($request->location['country_code'])) {
            $countryId = $this->helpers->getCountryId($request->location['country_code']);
            $request->request->add(['country_id' => $countryId]);
        }
        if (isset($request->location['city_id'])) {
            $request->request->add(['city_id' => $request->location['city_id']]);
        }
        if (isset($request->organisation['organisation_id'])) {
            $request->request->add(['organisation_id' => $request->organisation['organisation_id']]);
        }
        if (isset($request->organisation['organisation_name'])) {
            $request->request->add(['organisation_name' => $request->organisation['organisation_name']]);
        }

        $mission = $this->mission->findOrFail($id);
        $mission->update($request->toArray());

        // update goal_mission details
        if ($request->mission_type == config('constants.mission_type.GOAL')) {
            $goalMissionArray = array(
                'goal_objective' => $request->goal_objective
            );
            $mission->goalMission()->update($goalMissionArray);
        }
        // update into time_mission details
        if ($request->mission_type == config('constants.mission_type.TIME')) {
            $missionDetail = $mission->timeMission()->first();
            if (!is_null($missionDetail)) {
                $missionDetail->application_deadline = (isset($request->application_deadline))
                ? $request->application_deadline : null;
                $missionDetail->application_start_date = (isset($request->application_start_date))
                ? $request->application_start_date : null;
                $missionDetail->application_end_date = (isset($request->application_end_date))
                ? $request->application_end_date : null;
                $missionDetail->application_start_time = (isset($request->application_start_time))
                ? $request->application_start_time : null;
                $missionDetail->application_end_time = (isset($request->application_end_time))
                ? $request->application_end_time : null;

                $missionDetail->save();
            }
        }

        // Add/Update mission title
        if (isset($request->mission_detail)) {
            foreach ($request->mission_detail as $value) {
                $language = $languages->where('code', $value['lang'])->first();
                $missionLanguage = array('mission_id' => $id,
                                        'language_id' => $language->language_id,
                                        'title' => $value['title'],
                                        'short_description' => (isset($value['short_description'])) ?
                                        $value['short_description'] : null,
                                        'description' => ($value['section']),
                                        'objective' => $value['objective']);

                $this->missionLanguage->createOrUpdateLanguage(['mission_id' => $id,
                'language_id' => $language->language_id], $missionLanguage);
                    
                unset($missionLanguage);
            }
        }
        
        // For skills
        if (isset($request->skills) && count($request->skills) > 0) {
            foreach ($request->skills as $value) {
                $this->missionSkill->linkMissionSkill($mission->mission_id, $value['skill_id']);
            }
        }

        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        // Add/Update  mission media images
        $isDefault = 0;
        if (isset($request->media_images) && count($request->media_images) > 0) {
            foreach ($request->media_images as $value) {
                $filePath = $this->s3helper->uploadFileOnS3Bucket($value['media_path'], $tenantName);
                // Check for default image in mission_media
                $default = (isset($value['default']) && ($value['default'] != '')) ? $value['default'] : '0';
                if ($default == '1') {
                    $isDefault = 1;
                    $media = array('default' => '0');
                    $this->missionMedia->where('mission_id', $id)->update($media);
                }
                
                $missionMedia = array('mission_id' => $id,
                                      'media_name' => $value['media_name'],
                                      'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                      'media_path' => $filePath,
                                      'default' => $default);
                
                $this->missionMedia->createOrUpdateMedia(['mission_id' => $id,
                 'mission_media_id' => $value['media_id']], $missionMedia);
                unset($missionMedia);
            }
            $defaultData = $this->missionMedia->where('mission_id', $id)
                                        ->where('default', '1')->count();
                                        
            if (($isDefault == 0) && ($defaultData == 0)) {
                $mediaData = $this->missionMedia->where('mission_id', $id)->orderBy('mission_media_id', 'ASC')->first();
                $missionMedia = array('default' => '1');
                $this->missionMedia->where('mission_media_id', $mediaData->mission_media_id)->update($missionMedia);
            }
        }

        // Add/Update mission media videos
        if (isset($request->media_videos) && count($request->media_videos) > 0) {
            foreach ($request->media_videos as $value) {
                $missionMedia = array('mission_id' => $id,
                                      'media_name' => $value['media_name'],
                                      'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                      'media_path' => $value['media_path']);

                $this->missionMedia->createOrUpdateMedia(['mission_id' => $id,
                 'mission_media_id' => $value['media_id']], $missionMedia);
                unset($missionMedia);
            }
        }
        // Add/Update mission documents
        if (isset($request->documents) && count($request->documents) > 0) {
            foreach ($request->documents as $value) {
                $missionDocument = array('mission_id' => $id,
                                        'document_name' => $value['document_name'],
                                        'document_type' => pathinfo($value['document_name'], PATHINFO_EXTENSION)
                                      );
                if ($value['document_path'] != '') {
                    $filePath = $this->s3helper->uploadFileOnS3Bucket($value['document_path'], $tenantName);
                    $missionDocument['document_path'] = $filePath;
                }
                
                $this->missionDocument->createOrUpdateDocument(['mission_id' => $id,
                 'mission_document_id' => $value['document_id']], $missionDocument);
                unset($missionDocument);
            }
        }
        return $mission;
    }
    
    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return App\Models\Mission
     */
    public function find(int $id): Mission
    {
        return $this->mission->
        with(
            'missionMedia',
            'missionDocument',
            'missionTheme',
            'city',
            'country',
            'missionLanguage',
            'timeMission',
            'goalMission'
        )->findOrFail($id);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        return $this->mission->deleteMission($id);
    }

    /**
     * Display a listing of mission.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionList(Request $request): LengthAwarePaginator
    {
        $languages = $this->languageHelper->getLanguages($request);
        $mission = Mission::select(
            'mission.mission_id',
            'mission.theme_id',
            'mission.city_id',
            'mission.country_id',
            'mission.start_date',
            'mission.end_date',
            'mission.total_seats',
            'mission.mission_type',
            'mission.publication_status',
            'mission.organisation_id',
            'mission.organisation_name'
        )
        ->with(['city', 'country', 'missionTheme',
        'missionLanguage', 'missionMedia', 'missionDocument', 'goalMission', 'timeMission'])
        ->withCount('missionApplication')
        ->paginate($request->perPage);

        foreach ($mission as $key => $value) {
            foreach ($value->missionLanguage as $languageValue) {
                $languageData = $languages->where('language_id', $languageValue->language_id)->first();
                $languageValue->lang = $languageData->code;
            }
            foreach ($value->missionMedia as $mediaValue) {
                if ($mediaValue->default == 1) {
                    $value->default_media_name = $mediaValue->media_name;
                    $value->default_media_type = $mediaValue->media_type;
                    $value->default_media_path = $mediaValue->media_path;
                }
            }
        }
        return $mission;
    }

    /**
     * Display a listing of mission.
     *
     * @param Illuminate\Http\Request $request
     * @param Array $userFilterData
     * @param int $languageId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function appMissions(Request $request, array $userFilterData, int $languageId): LengthAwarePaginator
    {
        $missionData = [];
        // Get  mission data
        $missionQuery = $this->mission->select('mission.*');
        $missionQuery->leftjoin('time_mission', 'mission.mission_id', '=', 'time_mission.mission_id');
        $missionQuery->where('publication_status', config("constants.publication_status")["APPROVED"])
            ->with(['missionTheme', 'missionMedia', 'goalMission'
            ])->with(['missionMedia' => function ($query) {
                $query->where('status', '1');
                $query->where('default', '1');
            }])
            ->with(['missionLanguage' => function ($query) use ($languageId) {
                $query->select('mission_language_id', 'mission_id', 'title', 'short_description', 'objective')
                ->where('language_id', $languageId);
            }])
            ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['missionApplication as mission_application_count' => function ($query) use ($request) {
                $query->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['favouriteMission as favourite_mission_count' => function ($query) use ($request) {
                $query->Where('user_id', $request->auth->user_id);
            }]);
        $missionQuery->withCount([
                'missionRating as mission_rating_count' => function ($query) {
                    $query->select(DB::raw("AVG(rating) as rating"));
                }
            ]);
        $missionQuery->with(['missionRating']);
        //Explore mission by top favourite
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.TOP_FAVOURITE'))) {
            $missionQuery->withCount(['favouriteMission as favourite_mission_count']);
            $missionQuery->orderBY('favourite_mission_count', 'desc');
        }

        //Explore mission by most ranked
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.MOST_RANKED'))) {
            $missionQuery->orderBY('mission_rating_count', 'desc');
        }

        //Explore mission recommended to user
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.TOP_RECOMMENDED'))) {
            $missionQuery->withCount(['missionInvite as mission_invite_count' => function ($query) use ($request) {
                $query->where('to_user_id', $request->auth->user_id);
            }]);

            $missionQuery->orderBY('mission_invite_count', 'desc');
            $missionQuery->whereHas('missionInvite', function ($countryQuery) use ($request) {
                $countryQuery->where('to_user_id', $request->auth->user_id);
            });
        }

        //Explore mission by random
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.RANDOM'))) {
            $missionQuery->inRandomOrder();
        }
        
        // Explore mission by country
        if ($request->has('explore_mission_type') && $request->input('explore_mission_type') != '') {
            if ($request->input('explore_mission_type') == config('constants.THEME')) {
                $missionQuery->Where("mission.theme_id", $request->input('explore_mission_params'));
            }
            if ($request->input('explore_mission_type') == config('constants.COUNTRY')) {
                $missionQuery->Where(function ($query) use ($request) {
                    $query->wherehas('country', function ($countryQuery) use ($request) {
                        $countryQuery->where('name', 'like', '%' . $request->input('explore_mission_params') . '%');
                    });
                });
            }
            if ($request->input('explore_mission_type') == config('constants.ORGANIZATION')) {
                $missionQuery->Where(
                    'organisation_name',
                    'like',
                    '%' . $request->input('explore_mission_params') . '%'
                );
            }
        }
        
        //Explore mission by theme
        if ($userFilterData['search'] && $userFilterData['search'] != '') {
            $missionQuery->Where(function ($query) use ($userFilterData) {
                $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($userFilterData) {
                    $missionLanguageQuery->Where('title', 'like', '%' . $userFilterData['search'] . '%');
                    $missionLanguageQuery->orWhere('short_description', 'like', '%' . $userFilterData['search'] . '%');
                });
                $query->orWhere(function ($organizationQuery) use ($userFilterData) {
                    $organizationQuery->orWhere('organisation_name', 'like', '%' . $userFilterData['search'] . '%');
                });
            });
        }

        if ($userFilterData['country_id'] && $userFilterData['country_id'] != '') {
            $missionQuery->Where("mission.country_id", $userFilterData['country_id']);
        }

        if ($userFilterData['city_id'] && $userFilterData['city_id'] != '') {
            $missionQuery->whereIn("mission.city_id", explode(",", $userFilterData['city_id']));
        }

        if ($userFilterData['theme_id'] && $userFilterData['theme_id'] != '') {
            $missionQuery->whereIn("mission.theme_id", explode(",", $userFilterData['theme_id']));
        }

        if ($userFilterData['skill_id'] && $userFilterData['skill_id'] != '') {
            $missionQuery->wherehas('missionSkill', function ($skillQuery) use ($userFilterData) {
                $skillQuery->whereIn("skill_id", explode(",", $userFilterData['skill_id']));
            });
        }

        if ($userFilterData['sort_by'] && $userFilterData['sort_by'] != '') {
            if ($userFilterData['sort_by'] == config('constants.NEWEST')) {
                $missionQuery->orderBY('mission.created_at', 'desc');
            }
            if ($userFilterData['sort_by'] == config('constants.OLDEST')) {
                $missionQuery->orderBY('mission.created_at', 'asc');
            }
            if ($userFilterData['sort_by'] == config('constants.LOWEST_AVAILABLE_SEATS')) {
                $missionQuery->orderByRaw('total_seats - mission_application_count asc');
            }
            if ($userFilterData['sort_by'] == config('constants.HIGHEST_AVAILABLE_SEATS')) {
                $missionQuery->orderByRaw('total_seats - mission_application_count desc');
            }
            if ($userFilterData['sort_by'] == config('constants.MY_FAVOURITE')) {
                $missionQuery->withCount(['favouriteMission as favourite_mission_count'
                    => function ($query) use ($request) {
                        $query->Where('user_id', $request->auth->user_id);
                    }]);
                $missionQuery->orderBY('favourite_mission_count', 'desc');
            }
            if ($userFilterData['sort_by'] == config('constants.DEADLINE')) {
                $missionQuery->orderBy(
                    \DB::raw('time_mission.application_deadline IS NULL, time_mission.application_deadline'),
                    'asc'
                );
            }
        }
        $mission =  $missionQuery->paginate($request->perPage);
        return $mission;
    }

    /**
     * Display a Explore mission data.
     *
     * @param Illuminate\Http\Request $request
     * @param string $topFilterData
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function exploreMission(Request $request, string $topFilterParams): Collection
    {
        // Get  mission data
        $missionQuery = $this->mission->select('*')
        ->where('publication_status', config("constants.publication_status")["APPROVED"]);
        switch ($topFilterParams) {
            case config('constants.TOP_THEME'):
                $missionQuery
                ->selectRaw('COUNT(mission.theme_id) as mission_theme_count')
                ->with(['missionTheme'])
                ->groupBy('mission.theme_id')
                ->orderBY('mission_theme_count', 'desc');
                break;
            case config('constants.TOP_COUNTRY'):
                $missionQuery->with(['country'])
                ->selectRaw('COUNT(mission.country_id) as mission_country_count')
                ->groupBy('mission.country_id')
                ->orderBY('mission_country_count', 'desc');
                break;
            case config('constants.TOP_ORGANISATION'):
                $missionQuery->selectRaw('COUNT(mission.organisation_id) as mission_organisation_count')
                ->groupBy('mission.organisation_id')
                ->orderBY('mission_organisation_count', 'desc');
                break;
        }
        $mission = $missionQuery->limit(config('constants.EXPLORE_MISSION_LIMIT'))->get();
        return $mission;
    }

    /**
     * Display mission filter data.
     *
     * @param Illuminate\Http\Request $request
     * @param string $filterParams
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function missionFilter(Request $request, string $filterParams): Collection
    {
        // Get  mission filter data
        switch ($filterParams) {
            case config('constants.COUNTRY'):
                $missionQuery = $this->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );
                $missionQuery->with(['country'])
                ->selectRaw('COUNT(mission.mission_id) as mission_count')
                ->groupBy('mission.country_id');
                $mission = $missionQuery->get();
                return $mission;
                break;

            case config('constants.CITY'):
                $missionQuery = $this->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );
                $missionQuery->with(['city'])
                ->selectRaw('COUNT(mission.mission_id) as mission_count');
                if ($request->has('country_id') && $request->input('country_id') != '') {
                    $missionQuery->Where("mission.country_id", $request->input('country_id'));
                }
                $missionQuery->groupBy('mission.city_id');
                $mission = $missionQuery->get();
                return $mission;
                break;

            case config('constants.THEME'):
                $missionQuery = $this->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );
                $missionQuery->with(['missionTheme'])
                ->selectRaw('COUNT(mission.mission_id) as mission_count');
                if ($request->has('country_id') && $request->input('country_id') != '') {
                    $missionQuery->Where("mission.country_id", $request->input('country_id'));
                }
                if ($request->has('city_id') && $request->input('city_id') != '') {
                    $missionQuery->whereIn("mission.city_id", explode(",", $request->input('city_id')));
                }
                $missionQuery->groupBy('mission.theme_id');
                $mission = $missionQuery->get();
                return $mission;
                break;

            case config('constants.SKILL'):
                $skillQuery = $this->missionSkill->select('*');
                $skillQuery->selectRaw('COUNT(mission_id) as mission_count');
                $skillQuery->wherehas('mission', function ($query) use ($request) {
                    if ($request->has('country_id') && $request->input('country_id') != '') {
                        $query->Where("mission.country_id", $request->input('country_id'));
                    }
                    if ($request->has('city_id') && $request->input('city_id') != '') {
                        $query->whereIn("mission.city_id", explode(",", $request->input('city_id')));
                    }
                    if ($request->has('theme_id') && $request->input('theme_id') != '') {
                        $query->whereIn("mission.theme_id", explode(",", $request->input('theme_id')));
                    }
                });

                $skillQuery->with('mission', 'skill');
                $skillQuery->groupBy('skill_id');
                $skillQuery->orderBy('mission_count', 'desc');
                $skill = $skillQuery->get();
                return $skill;
                break;
        }
    }

    /**
     * Add/remove mission to favourite.
     *
     * @param int $userId
     * @param int $missionId
     * @return mixed
     */
    public function missionFavourite(int $userId, int $missionId)
    {
        $mission = $this->mission->findOrFail($missionId);
        $favouriteMission = $this->favouriteMission->findFavourite($userId, $missionId);

        if (is_null($favouriteMission)) {
            $favouriteMissions = $this->favouriteMission->addToFavourite($userId, $missionId);
        } else {
            $favouriteMissions =  $favouriteMission->removeFromFavourite($userId, $missionId);
        }
        return $this->favouriteMission->findFavourite($userId, $missionId);
    }

    /*
     * Get mission name.
     *
     * @param int $missionId
     * @param int $languageId
     * @return string
     */
    public function getMissionName(int $missionId, $languageId): string
    {
        return $this->missionLanguage->getMissionName($missionId, $languageId);
    }

    /**
     * Add/update mission rating.
     *
     * @param int $userId
     * @param array $request
     * @return App\Models\MissionRating
     */
    public function storeMissionRating(int $userId, array $request): MissionRating
    {
        $missionRating = array('rating' => $request['rating']);
        return $this->missionRating->createOrUpdateRating(['mission_id' => $request['mission_id'],
        'user_id' => $userId], $missionRating);
    }
    
    /**
     *Get detail of mission.
     *
     * @param Illuminate\Http\Request $request
     * @param Array $userFilterData
     * @param int $languageId
     * @param int $misionId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function appMission(
        Request $request,
        array $userFilterData,
        int $languageId,
        int $misionId
    ): LengthAwarePaginator {
        $missionData = [];
        // Get  mission data
        $missionQuery = $this->mission->select('mission.*');
        $missionQuery->leftjoin('time_mission', 'mission.mission_id', '=', 'time_mission.mission_id');
        $missionQuery->where('publication_status', config("constants.publication_status")["APPROVED"])
            ->with(['missionTheme', 'missionMedia', 'goalMission'
            ])->with(['missionMedia' => function ($query) {
                $query->where('status', '1');
                $query->where('default', '1');
            }])
            ->with(['missionLanguage' => function ($query) use ($languageId) {
                $query->select('mission_language_id', 'mission_id', 'title', 'short_description', 'objective')
                ->where('language_id', $languageId);
            }])
            ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['missionApplication as mission_application_count' => function ($query) use ($request) {
                $query->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['favouriteMission as favourite_mission_count' => function ($query) use ($request) {
                $query->Where('user_id', $request->auth->user_id);
            }]);
        $missionQuery->withCount([
                'missionRating as mission_rating_count' => function ($query) {
                    $query->select(DB::raw("AVG(rating) as rating"));
                }
            ]);
        $missionQuery->with(['missionRating']);
        //Explore mission by top favourite
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.TOP_FAVOURITE'))) {
            $missionQuery->withCount(['favouriteMission as favourite_mission_count']);
            $missionQuery->orderBY('favourite_mission_count', 'desc');
        }

        //Explore mission by most ranked
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.MOST_RANKED'))) {
            $missionQuery->orderBY('mission_rating_count', 'desc');
        }

        //Explore mission recommended to user
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.TOP_RECOMMENDED'))) {
            $missionQuery->withCount(['missionInvite as mission_invite_count' => function ($query) use ($request) {
                $query->where('to_user_id', $request->auth->user_id);
            }]);

            $missionQuery->orderBY('mission_invite_count', 'desc');
            $missionQuery->whereHas('missionInvite', function ($countryQuery) use ($request) {
                $countryQuery->where('to_user_id', $request->auth->user_id);
            });
        }

        //Explore mission by random
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') == config('constants.RANDOM'))) {
            $missionQuery->inRandomOrder();
        }
        
        // Explore mission by country
        if ($request->has('explore_mission_type') && $request->input('explore_mission_type') != '') {
            if ($request->input('explore_mission_type') == config('constants.THEME')) {
                $missionQuery->Where("mission.theme_id", $request->input('explore_mission_params'));
            }
            if ($request->input('explore_mission_type') == config('constants.COUNTRY')) {
                $missionQuery->Where(function ($query) use ($request) {
                    $query->wherehas('country', function ($countryQuery) use ($request) {
                        $countryQuery->where('name', 'like', '%' . $request->input('explore_mission_params') . '%');
                    });
                });
            }
            if ($request->input('explore_mission_type') == config('constants.ORGANIZATION')) {
                $missionQuery->Where(
                    'organisation_name',
                    'like',
                    '%' . $request->input('explore_mission_params') . '%'
                );
            }
        }
        
        //Explore mission by theme
        if ($userFilterData['search'] && $userFilterData['search'] != '') {
            $missionQuery->Where(function ($query) use ($userFilterData) {
                $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($userFilterData) {
                    $missionLanguageQuery->Where('title', 'like', '%' . $userFilterData['search'] . '%');
                    $missionLanguageQuery->orWhere('short_description', 'like', '%' . $userFilterData['search'] . '%');
                });
                $query->orWhere(function ($organizationQuery) use ($userFilterData) {
                    $organizationQuery->orWhere('organisation_name', 'like', '%' . $userFilterData['search'] . '%');
                });
            });
        }

        if ($userFilterData['country_id'] && $userFilterData['country_id'] != '') {
            $missionQuery->Where("mission.country_id", $userFilterData['country_id']);
        }

        if ($userFilterData['city_id'] && $userFilterData['city_id'] != '') {
            $missionQuery->whereIn("mission.city_id", explode(",", $userFilterData['city_id']));
        }

        if ($userFilterData['theme_id'] && $userFilterData['theme_id'] != '') {
            $missionQuery->whereIn("mission.theme_id", explode(",", $userFilterData['theme_id']));
        }

        if ($userFilterData['skill_id'] && $userFilterData['skill_id'] != '') {
            $missionQuery->wherehas('missionSkill', function ($skillQuery) use ($userFilterData) {
                $skillQuery->whereIn("skill_id", explode(",", $userFilterData['skill_id']));
            });
        }

        if ($userFilterData['sort_by'] && $userFilterData['sort_by'] != '') {
            if ($userFilterData['sort_by'] == config('constants.NEWEST')) {
                $missionQuery->orderBY('mission.created_at', 'desc');
            }
            if ($userFilterData['sort_by'] == config('constants.OLDEST')) {
                $missionQuery->orderBY('mission.created_at', 'asc');
            }
            if ($userFilterData['sort_by'] == config('constants.LOWEST_AVAILABLE_SEATS')) {
                $missionQuery->orderByRaw('total_seats - mission_application_count asc');
            }
            if ($userFilterData['sort_by'] == config('constants.HIGHEST_AVAILABLE_SEATS')) {
                $missionQuery->orderByRaw('total_seats - mission_application_count desc');
            }
            if ($userFilterData['sort_by'] == config('constants.MY_FAVOURITE')) {
                $missionQuery->withCount(['favouriteMission as favourite_mission_count'
                    => function ($query) use ($request) {
                        $query->Where('user_id', $request->auth->user_id);
                    }]);
                $missionQuery->orderBY('favourite_mission_count', 'desc');
            }
            if ($userFilterData['sort_by'] == config('constants.DEADLINE')) {
                $missionQuery->orderBy(
                    \DB::raw('time_mission.application_deadline IS NULL, time_mission.application_deadline'),
                    'asc'
                );
            }
        }
        $mission =  $missionQuery->paginate($request->perPage);
        return $mission;
    }
}
