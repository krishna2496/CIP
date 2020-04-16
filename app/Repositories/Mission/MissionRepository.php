<?php
namespace App\Repositories\Mission;

use App\Repositories\Mission\MissionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\S3Helper;
use App\Models\Mission;
use App\Models\FavouriteMission;
use App\Models\MissionRating;
use App\Models\MissionApplication;
use App\Models\MissionDocument;
use App\Repositories\Country\CountryRepository;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Repositories\MissionMedia\MissionMediaRepository;
use App\Services\Mission\ModelsService;

class MissionRepository implements MissionInterface
{
    /**
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
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;

    /**
    * @var App\Repositories\MissionMedia\MissionMediaRepository
    */
    private $missionMediaRepository;

    /**
    * @var App\Services\Mission\ModelsService
    */
    private $modelsService;
    
    /**
     * Create a new Mission repository instance.
     *
     * @param  App\Helpers\LanguageHelper $languageHelper
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @param  App\Repositories\Country\CountryRepository $countryRepository
     * @param  App\Repositories\MissionMedia\MissionMediaRepository $missionMediaRepository
     * @param  App\Services\Mission\ModelsService $modelsService
     * @return void
     */
    public function __construct(
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        CountryRepository $countryRepository,
        MissionMediaRepository $missionMediaRepository,
        ModelsService $modelsService
    ) {
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
        $this->countryRepository = $countryRepository;
        $this->missionMediaRepository = $missionMediaRepository;
        $this->modelsService = $modelsService;
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Mission
     */
    public function store(Request $request): Mission
    {
        $languages = $this->languageHelper->getLanguages();
        $countryId = $this->countryRepository->getCountryId($request->location['country_code']);
        $missionData = array(
                'theme_id' => $request->theme_id != "" ? $request->theme_id : null,
                'city_id' => $request->location['city_id'],
                'state_id' => (isset($request->location['state_id'])) ? $request->location['state_id'] : null,
                'country_id' => $countryId,
                'start_date' => (isset($request->start_date)) ? $request->start_date : null,
                'end_date' => (isset($request->end_date)) ? $request->end_date : null,
                'total_seats' => (isset($request->total_seats) && ($request->total_seats !== '')) ?
                 $request->total_seats : null,
                'publication_status' => $request->publication_status,
                'organisation_id' => $request->organisation['organisation_id'],
                'organisation_name' => $request->organisation['organisation_name'],
                'organisation_detail' => (isset($request->organisation['organisation_detail'])) ?
                $request->organisation['organisation_detail'] : null,
                'availability_id' => $request->availability_id,
                'mission_type' => $request->mission_type,
                'is_virtual' => (isset($request->is_virtual)) ? $request->is_virtual : '0',
            );
        
        // Create new record
        $mission = $this->modelsService->mission->create($missionData);

        // Entry into goal_mission table
        if ($request->mission_type === config('constants.mission_type.GOAL') && isset($request->goal_objective)) {
            $goalMissionArray = array(
                'goal_objective' => $request->goal_objective
            );
            $mission->goalMission()->create($goalMissionArray);
        }

        // Entry into time_mission table
        if ($request->mission_type == "TIME") {
            $timeMissionArray = array(
                'application_deadline' => (isset($request->application_deadline)
                && $request->application_deadline !== '') ?
                $request->application_deadline : null,
                'application_start_date' => (isset($request->application_start_date)
                && $request->application_start_date !== '')
                ? $request->application_start_date : null,
                'application_end_date' => (isset($request->application_end_date)
                && $request->application_end_date !== '')
                ? $request->application_end_date : null,
                'application_start_time' => (isset($request->application_start_time)
                && $request->application_start_time !== '')
                ? $request->application_start_time : null,
                'application_end_time' => (isset($request->application_end_time)
                && $request->application_end_time !== '')
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
                    'objective' =>  (isset($value['objective'])) ? $value['objective'] : null,
                    'custom_information' => (array_key_exists('custom_information', $value))
                    ? $value['custom_information'] : null,
                    'label_goal_achieved' => (isset($value['label_goal_achieved'])) ? $value['label_goal_achieved']
                    : null,
                    'label_goal_objective' => (isset($value['label_goal_objective'])) ? $value['label_goal_objective']
                    : null
                );

            $this->modelsService->missionLanguage->create($missionLanguage);
            unset($missionLanguage);
        }
        
        // For skills
        if (isset($request->skills) && count($request->skills) > 0) {
            foreach ($request->skills as $value) {
                $this->modelsService->missionSkill->linkMissionSkill($mission->mission_id, $value['skill_id']);
            }
        }
        
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        // Add mission media images
        if (isset($request->media_images) && count($request->media_images) > 0) {
            $this->missionMediaRepository->saveMediaImages($request->media_images, $tenantName, $mission->mission_id);
        }
        // Add mission media videos
        if (isset($request->media_videos) && count($request->media_videos) > 0) {
            if (!empty($request->media_videos)) {
                $this->missionMediaRepository->saveMediaVideos($request->media_videos, $mission->mission_id);
            }
        }
            
        // Add mission documents
        if (isset($request->documents) && count($request->documents) > 0) {
            if (!empty($request->documents)) {
                foreach ($request->documents as $value) {
                    $filePath = $this->s3helper->uploadMissionDocumentOnS3Bucket($value['document_path'], $tenantName);
                    $missionDocument = array('mission_id' => $mission->mission_id,
                                            'document_name' => basename($filePath),
                                            'document_type' => pathinfo(basename($filePath), PATHINFO_EXTENSION),
                                            'document_path' => $filePath,
                                            'sort_order' => $value['sort_order']);
                    $this->modelsService->missionDocument->create($missionDocument);
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
        $languages = $this->languageHelper->getLanguages();
        // Set data for update record
        if (isset($request->location['country_code'])) {
            $countryId = $this->countryRepository->getCountryId($request->location['country_code']);
            $request->request->add(['country_id' => $countryId]);
        }
        if (isset($request->location['city_id'])) {
            $request->request->add(['city_id' => $request->location['city_id']]);
        }
        if (isset($request->location['state_id'])) {
            $request->request->add(['state_id' => $request->location['state_id']]);
        }
        if (isset($request->organisation['organisation_id'])) {
            $request->request->add(['organisation_id' => $request->organisation['organisation_id']]);
        }
        if (isset($request->organisation['organisation_name'])) {
            $request->request->add(['organisation_name' => $request->organisation['organisation_name']]);
        }
        if (isset($request->organisation['organisation_detail'])) {
            $request->request->add(['organisation_detail' => $request->organisation['organisation_detail']]);
        }
        if (isset($request->total_seats)) {
            $totalSeats = (isset($request->total_seats) && (trim($request->total_seats) !== '')) ?
            $request->total_seats : null;
            $totalSeats = ($totalSeats !== null) ? abs($totalSeats) : $totalSeats;
            $request->request->add(['total_seats' => $totalSeats]);
        }

        if (isset($request->total_seats) && ($request->total_seats === '')) {
            $request->request->set('total_seats', null);
        }

        if (isset($request->theme_id) && ($request->theme_id === '')) {
            $request->request->set('theme_id', null);
        }

        $mission = $this->modelsService->mission->findOrFail($id);
        $mission->update($request->toArray());

        // update goal_mission details
        if ($mission->mission_type === config('constants.mission_type.GOAL') && (isset($request->goal_objective))) {
            $goalMissionArray = array(
                'goal_objective' => $request->goal_objective
            );
            $mission->goalMission()->update($goalMissionArray);
        }
        // update into time_mission details
        if ($mission->mission_type === config('constants.mission_type.TIME')) {
            $missionDetail = $mission->timeMission()->first();
            if (!is_null($missionDetail)) {
                if ((isset($request->application_deadline))) {
                    $missionDetail->application_deadline = ($request->application_deadline !== '') ?
                    $request->application_deadline : null;
                }
                if ((isset($request->application_start_date))) {
                    $missionDetail->application_start_date = ($request->application_start_date !== '')
                    ? $request->application_start_date : null;
                }
                if ((isset($request->application_end_date))) {
                    $missionDetail->application_end_date = ($request->application_end_date !== '')
                    ? $request->application_end_date : null;
                }
                if ((isset($request->application_start_time))) {
                    $missionDetail->application_start_time = ($request->application_start_time !== '')
                    ? $request->application_start_time : null;
                }
                if ((isset($request->application_end_time))) {
                    $missionDetail->application_end_time = ($request->application_end_time !== '')
                    ? $request->application_end_time : null;
                }
                $missionDetail->save();
            }
        }

        // Add/Update mission title
        if (isset($request->mission_detail)) {
            foreach ($request->mission_detail as $value) {
                $missionLanguageDeleteFlag = 0;
                $language = $languages->where('code', $value['lang'])->first();
                $missionLanguage = array('mission_id' => $id,
                                        'language_id' => $language->language_id
                                        );
                if (array_key_exists('custom_information', $value)) {
                    $missionLanguage['custom_information'] = $value['custom_information'];
                }
                if (array_key_exists('title', $value)) {
                    $missionLanguage['title'] = $value['title'];
                }
                if (array_key_exists('short_description', $value)) {
                    $missionLanguage['short_description'] = $value['short_description'];
                }
                if (array_key_exists('objective', $value)) {
                    $missionLanguage['objective'] = $value['objective'];
                }

                if (array_key_exists('label_goal_achieved', $value)) {
                    $missionLanguage['label_goal_achieved'] = $value['label_goal_achieved'];
                }

                if (array_key_exists('label_goal_objective', $value)) {
                    $missionLanguage['label_goal_objective'] = $value['label_goal_objective'];
                }
                 
                if (array_key_exists('section', $value)) {
                    if (empty($value['section'])) {
                        $this->modelsService->missionLanguage->deleteMissionLanguage($id, $language->language_id);
                        $missionLanguageDeleteFlag = 1;
                    } else {
                        $missionLanguage['description'] = $value['section'];
                    }
                }

                if ($missionLanguageDeleteFlag !== 1) {
                    $this->modelsService->missionLanguage->createOrUpdateLanguage(['mission_id' => $id,
                    'language_id' => $language->language_id], $missionLanguage);
                }
                unset($missionLanguage);
            }
        }
        
        // For skills
        if (isset($request->skills) && count($request->skills) > 0) {
            //Unlink mission skill
            $this->modelsService->missionSkill->unlinkMissionSkill($mission->mission_id);
            
            // Link mission skill
            foreach ($request->skills as $value) {
                $this->modelsService->missionSkill->linkMissionSkill($mission->mission_id, $value['skill_id']);
            }
        }

        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        // Add/Update  mission media images
        if (isset($request->media_images) && count($request->media_images) > 0) {
            $this->missionMediaRepository->updateMediaImages($request->media_images, $tenantName, $id);
        }

        // Add/Update mission media videos
        if (isset($request->media_videos) && count($request->media_videos) > 0) {
            $this->missionMediaRepository->updateMediaVideos($request->media_videos, $id);
        }
        // Add/Update mission documents
        if (isset($request->documents) && count($request->documents) > 0) {
            foreach ($request->documents as $value) {
                $missionDocument = array('mission_id' => $id);
                if (isset($value['document_path'])) {
                    $filePath = $this->s3helper->uploadMissionDocumentOnS3Bucket($value['document_path'], $tenantName);
                    $missionDocument['document_path'] = $filePath;
                    $missionDocument['document_name'] = basename($filePath);
                    $missionDocument['document_type'] = pathinfo($filePath, PATHINFO_EXTENSION);
                }
                if (isset($value['sort_order'])) {
                    $missionDocument['sort_order'] = $value['sort_order'];
                }
                
                $this->modelsService->missionDocument->createOrUpdateDocument(['mission_id' => $id,
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
        $mission = $this->modelsService->mission->
        with(
            'missionTheme',
            'city.languages',
            'state.languages',
            'country.languages',
            'missionLanguage',
            'timeMission',
            'goalMission'
        )->with(['missionSkill' => function ($query) {
            $query->with('mission', 'skill');
        }])->with(['missionMedia' => function ($query) {
            $query->orderBy('sort_order');
        }])
        ->with(['missionDocument' => function ($query) {
            $query->orderBy('sort_order');
        }])->findOrFail($id);
        
        if (isset($mission->missionLanguage)) {
            $languages = $this->languageHelper->getLanguages();
            foreach ($mission->missionLanguage as $missionLanguage) {
                $missionLanguage['language_code'] = $languages->where(
                    'language_id',
                    $missionLanguage->language_id
                )->first()->code;
            }
        }
        return $mission;
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->modelsService->mission->deleteMission($id);
    }

    /**
     * Display a listing of mission.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionList(Request $request): LengthAwarePaginator
    {
        $languages = $this->languageHelper->getLanguages();
        $missionQuery = $this->modelsService->mission->select(
            'mission.mission_id',
            'mission.theme_id',
            'mission.city_id',
            'mission.state_id',
            'mission.country_id',
            'mission.start_date',
            'mission.end_date',
            'mission.total_seats',
            'mission.mission_type',
            'mission.publication_status',
            'mission.organisation_id',
            'mission.organisation_name',
            'mission.is_virtual'
        )
        ->with(['city.languages', 'country.languages', 'state.languages', 'missionTheme',
        'missionLanguage', 'goalMission', 'timeMission'])
        ->withCount('missionApplication')
        ->with(['missionSkill' => function ($query) {
            $query->with('mission', 'skill');
        }])
        ->with(['missionMedia' => function ($query) {
            $query->orderBy('sort_order');
        }])
        ->with(['missionDocument' => function ($query) {
            $query->orderBy('sort_order');
        }]);
        
        if ($request->has('search') && $request->has('search') !== '') {
            $searchString = $request->search;
            $missionQuery->where(function ($query) use ($searchString) {
                $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($searchString) {
                    $missionLanguageQuery->where('title', 'like', '%' . $searchString . '%');
                    $missionLanguageQuery->orWhere('short_description', 'like', '%' . $searchString . '%');
                });
                $query->orWhere(function ($organizationQuery) use ($searchString) {
                    $organizationQuery->orWhere('organisation_name', 'like', '%' . $searchString . '%');
                });
            });
        }
        
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $missionQuery->orderBy('mission_id', $orderDirection);
        }
        $mission = $missionQuery->paginate($request->perPage);

        foreach ($mission as $key => $value) {
            foreach ($value->missionLanguage as $languageValue) {
                $languageData = $languages->where('language_id', $languageValue->language_id)->first();
                $languageValue->lang = $languageData->code;
            }
            foreach ($value->missionMedia as $mediaValue) {
                if ($mediaValue->default === '1') {
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
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getMissions(Request $request, array $userFilterData): LengthAwarePaginator
    {
        $missionData = [];
        // Get  mission data
        $missionQuery = $this->modelsService->mission->select('mission.*');
       
        $missionQuery->leftjoin('time_mission', 'mission.mission_id', '=', 'time_mission.mission_id');
        $missionQuery->where('publication_status', config("constants.publication_status")["APPROVED"])
            ->with(['missionTheme', 'missionMedia', 'goalMission', 'availability'
            ])->with(['missionMedia' => function ($query) {
                $query->where('status', '1');
                $query->where('default', '1');
            }])
            ->with(['missionLanguage' => function ($query) {
                $query->select(
                    'mission_language_id',
                    'mission_id',
                    'language_id',
                    'title',
                    'short_description',
                    'objective',
                    'custom_information',
                    'label_goal_achieved',
                    'label_goal_objective'
                );
            }])
            ->with(['city.languages'])
            ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['missionApplication as mission_application_count' => function ($query) use ($request) {
                $query->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
            }])
            ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['favouriteMission as favourite_mission_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id);
            }]);
        $missionQuery->withCount([
                'missionRating as mission_rating_count' => function ($query) {
                    $query->select(DB::raw("AVG(rating) as rating"));
                }
            ]);
        $missionQuery->withCount([
            'timesheet AS achieved_goal' => function ($query) use ($request) {
                $query->select(DB::raw("SUM(action) as action"));
                $query->whereIn('status', array(config('constants.timesheet_status.APPROVED'),
                config('constants.timesheet_status.AUTOMATICALLY_APPROVED')));
            }]);
        $missionQuery->with(['missionRating']);
       
        //Explore mission recommended to user
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') === config('constants.TOP_RECOMMENDED'))) {
            $missionQuery->withCount(['missionInvite as mission_invite_count' => function ($query) use ($request) {
                $query->where('to_user_id', $request->auth->user_id);
            }]);

            $missionQuery->orderBY('mission_invite_count', 'desc');
            $missionQuery->whereHas('missionInvite', function ($countryQuery) use ($request) {
                $countryQuery->where('to_user_id', $request->auth->user_id);
            });
        }

        // Explore mission by country
        if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
            if ($request->input('explore_mission_type') === config('constants.THEME')) {
                $missionQuery->where("mission.theme_id", $request->input('explore_mission_params'));
            }
            if ($request->input('explore_mission_type') === config('constants.COUNTRY')) {
                $missionQuery->where(function ($query) use ($request) {
                    $query->wherehas('country', function ($countryQuery) use ($request) {
                        $countryQuery->where("mission.country_id", $request->input('explore_mission_params'));
                    });
                });
            }
            if ($request->input('explore_mission_type') === config('constants.ORGANIZATION')) {
                $missionQuery->where("mission.organisation_id", $request->input('explore_mission_params'));
            }
        }
        
        //Explore mission by theme
        if ($userFilterData['search'] && $userFilterData['search'] !== '') {
            $missionQuery->where(function ($query) use ($userFilterData) {
                $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($userFilterData) {
                    $missionLanguageQuery->where('title', 'like', '%' . $userFilterData['search'] . '%');
                    $missionLanguageQuery->orWhere('short_description', 'like', '%' . $userFilterData['search'] . '%');
                });
                $query->orWhere(function ($organizationQuery) use ($userFilterData) {
                    $organizationQuery->orWhere('organisation_name', 'like', '%' . $userFilterData['search'] . '%');
                });
            });
        }

        if ($userFilterData['country_id'] && $userFilterData['country_id'] !== '') {
            $missionQuery->where("mission.country_id", $userFilterData['country_id']);
        }

        if ($userFilterData['state_id'] && $userFilterData['state_id'] !== '') {
            $missionQuery->whereIn("mission.state_id", explode(",", $userFilterData['state_id']));
        }

        if ($userFilterData['city_id'] && $userFilterData['city_id'] !== '') {
            $missionQuery->whereIn("mission.city_id", explode(",", $userFilterData['city_id']));
        }

        if ($userFilterData['theme_id'] && $userFilterData['theme_id'] !== '') {
            $missionQuery->whereIn("mission.theme_id", explode(",", $userFilterData['theme_id']));
        }

        if ($userFilterData['skill_id'] && $userFilterData['skill_id'] !== '') {
            $missionQuery->wherehas('missionSkill', function ($skillQuery) use ($userFilterData) {
                $skillQuery->whereIn("skill_id", explode(",", $userFilterData['skill_id']));
            });
        }

        if ($userFilterData['sort_by'] && $userFilterData['sort_by'] !== '') {
            if ($userFilterData['sort_by'] === config('constants.NEWEST')) {
                $missionQuery->orderBY('mission.created_at', 'desc');
            }
            if ($userFilterData['sort_by'] === config('constants.OLDEST')) {
                $missionQuery->orderBY('mission.created_at', 'asc');
            }
            if ($userFilterData['sort_by'] === config('constants.LOWEST_AVAILABLE_SEATS')) {
                $missionQuery->orderByRaw('total_seats IS NULL, total_seats - mission_application_count ASC');
            }
            if ($userFilterData['sort_by'] === config('constants.HIGHEST_AVAILABLE_SEATS')) {
                $missionQuery->orderByRaw('total_seats IS NOT NULL, total_seats - mission_application_count DESC');
            }
            if ($userFilterData['sort_by'] === config('constants.MY_FAVOURITE')) {
                $missionQuery->withCount(['favouriteMission as favourite_mission_count'
                    => function ($query) use ($request) {
                        $query->where('user_id', $request->auth->user_id);
                    }]);
                $missionQuery->orderBY('favourite_mission_count', 'desc');
            }
            if ($userFilterData['sort_by'] === config('constants.DEADLINE')) {
                $missionQuery->orderBy(
                    \DB::raw('time_mission.application_deadline IS NULL, time_mission.application_deadline'),
                    'asc'
                );
            }
        }
        
        //Explore mission by top favourite
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') === config('constants.TOP_FAVOURITE'))) {
            $missionQuery->withCount(['favouriteMission as favourite_mission_counts']);
            $missionQuery = $missionQuery->having("favourite_mission_counts", '>', '0');
            $missionQuery->orderBY('favourite_mission_counts', 'desc');
        }

        //Explore mission by most ranked
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') === config('constants.MOST_RANKED'))) {
            $missionQuery->withCount(['missionRating as average_rating' => function ($query) use ($request) {
                $query->select(DB::raw("AVG(rating) as rating"));
            }]);
            $missionQuery = $missionQuery->having("average_rating", '>', '0');
            $missionQuery->orderBY('mission_rating_count', 'desc');
        }

        //Explore mission by random
        if ($request->has('explore_mission_type') &&
        ($request->input('explore_mission_type') === config('constants.RANDOM'))) {
            $missionQuery->inRandomOrder();
        }
        
        $page = $request->page ?? 1;
        $perPage = $request->perPage;
        $offSet = ($page-1) * $perPage;
        $totalCount = $missionQuery->get()->count();
        $missionData = $missionQuery->offset($offSet)->limit($perPage)->get();

        $paginate = new LengthAwarePaginator(
            $missionData,
            $totalCount,
            $perPage,
            $page,
            ['path'=>url($request->getPathInfo())]
        );
        return $paginate;
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
        $missionQuery = $this->modelsService->mission->select('*')
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
                $missionQuery->with(['country'=> function ($query) {
                    $query->with('languages');
                }])
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
                $missionQuery = $this->modelsService->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );

                if ($request->has('search') && $request->input('search') !== '') {
                    $missionQuery->where(function ($query) use ($request) {
                        $query->with('missionLanguage');
                        $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($request) {
                            $missionLanguageQuery->where('title', 'like', '%' . $request->input('search') . '%');
                            $missionLanguageQuery
                            ->orWhere('short_description', 'like', '%' . $request->input('search') . '%');
                        });
                        $query->orWhere(function ($organizationQuery) use ($request) {
                            $organizationQuery
                            ->orWhere('organisation_name', 'like', '%' . $request->input('search') . '%');
                        });
                    });
                }

                if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
                    if ($request->input('explore_mission_type') === config('constants.THEME')) {
                        $missionQuery->where("mission.theme_id", $request->input('explore_mission_params'));
                    }
                    if ($request->input('explore_mission_type') === config('constants.COUNTRY')) {
                        $missionQuery->where(function ($query) use ($request) {
                            $query->wherehas('country', function ($countryQuery) use ($request) {
                                $countryQuery->where("mission.country_id", $request->input('explore_mission_params'));
                            });
                        });
                    }
                    if ($request->input('explore_mission_type') === config('constants.ORGANIZATION')) {
                        $missionQuery->where(
                            'organisation_name',
                            'like',
                            '%' . $request->input('explore_mission_params') . '%'
                        );
                    }
                }

                $missionQuery->with(['country'=> function ($query) {
                    $query->with('languages');
                }])
                ->selectRaw('COUNT(mission.mission_id) as mission_count')
                ->groupBy('mission.country_id');
                $mission = $missionQuery->get();
                break;

            case config('constants.CITY'):
                $missionQuery = $this->modelsService->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );
                if ($request->has('search') && $request->input('search') !== '') {
                    $missionQuery->where(function ($query) use ($request) {
                        $query->with('missionLanguage');
                        $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($request) {
                            $missionLanguageQuery->where('title', 'like', '%' . $request->input('search') . '%');
                            $missionLanguageQuery
                            ->orWhere('short_description', 'like', '%' . $request->input('search') . '%');
                        });
                        $query->orWhere(function ($organizationQuery) use ($request) {
                            $organizationQuery
                            ->orWhere('organisation_name', 'like', '%' . $request->input('search') . '%');
                        });
                    });
                }
                if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
                    if ($request->input('explore_mission_type') === config('constants.THEME')) {
                        $missionQuery->where("mission.theme_id", $request->input('explore_mission_params'));
                    }
                    if ($request->input('explore_mission_type') === config('constants.COUNTRY')) {
                        $missionQuery->where(function ($query) use ($request) {
                            $query->wherehas('country', function ($countryQuery) use ($request) {
                                $countryQuery->where("mission.country_id", $request->input('explore_mission_params'));
                            });
                        });
                    }
                    if ($request->input('explore_mission_type') === config('constants.ORGANIZATION')) {
                        $missionQuery->where(
                            'organisation_name',
                            'like',
                            '%' . $request->input('explore_mission_params') . '%'
                        );
                    }
                }

                $missionQuery->with(['city'=> function ($query) {
                    $query->with('languages');
                }])
                ->selectRaw('COUNT(mission.mission_id) as mission_count');
                if ($request->has('country_id') && $request->input('country_id') !== '') {
                    $missionQuery->where("mission.country_id", $request->input('country_id'));
                }
                if ($request->has('state_id') && $request->input('state_id') !== '') {
                    $missionQuery->where("mission.state_id", $request->input('state_id'));
                }
                $missionQuery->groupBy('mission.city_id');
                $mission = $missionQuery->get();
                break;

            case config('constants.THEME'):
                $missionQuery = $this->modelsService->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );
                if ($request->has('search') && $request->input('search') !== '') {
                    $missionQuery->where(function ($query) use ($request) {
                        $query->with('missionLanguage');
                        $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($request) {
                            $missionLanguageQuery->where('title', 'like', '%' . $request->input('search') . '%');
                            $missionLanguageQuery
                            ->orWhere('short_description', 'like', '%' . $request->input('search') . '%');
                        });
                        $query->orWhere(function ($organizationQuery) use ($request) {
                            $organizationQuery
                            ->orWhere('organisation_name', 'like', '%' . $request->input('search') . '%');
                        });
                    });
                }
                if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
                    if ($request->input('explore_mission_type') === config('constants.THEME')) {
                        $missionQuery->where("mission.theme_id", $request->input('explore_mission_params'));
                    }
                    if ($request->input('explore_mission_type') === config('constants.COUNTRY')) {
                        $missionQuery->where(function ($query) use ($request) {
                            $query->wherehas('country', function ($countryQuery) use ($request) {
                                $countryQuery->where("mission.country_id", $request->input('explore_mission_params'));
                            });
                        });
                    }
                    if ($request->input('explore_mission_type') === config('constants.ORGANIZATION')) {
                        $missionQuery->where(
                            'organisation_name',
                            'like',
                            '%' . $request->input('explore_mission_params') . '%'
                        );
                    }
                }
                $missionQuery->with(['missionTheme'])
                ->selectRaw('COUNT(mission.mission_id) as mission_count');
                if ($request->has('country_id') && $request->input('country_id') !== '') {
                    $missionQuery->where("mission.country_id", $request->input('country_id'));
                }
                if ($request->has('state_id') && $request->input('state_id') !== '') {
                    $missionQuery->where("mission.state_id", $request->input('state_id'));
                }
                if ($request->has('city_id') && $request->input('city_id') !== '') {
                    $missionQuery->whereIn("mission.city_id", explode(",", $request->input('city_id')));
                }
                $missionQuery->groupBy('mission.theme_id');
                $mission = $missionQuery->get();
                break;

            case config('constants.SKILL'):
                $missionSkillQuery = $this->modelsService->missionSkill->select('*');
                $missionSkillQuery->selectRaw('COUNT(mission_id) as mission_count');
                $missionSkillQuery->wherehas('mission', function ($query) use ($request) {
                    $query->with('missionLanguage');
                    if ($request->has('search') && $request->input('search') !== '') {
                        $query->where(function ($searchQuery) use ($request) {
                            $searchQuery->wherehas('missionLanguage', function ($missionLanguageQuery) use ($request) {
                                $missionLanguageQuery->where('title', 'like', '%' . $request->input('search') . '%');
                                $missionLanguageQuery
                                ->orWhere('short_description', 'like', '%' . $request->input('search') . '%');
                            });
                            $searchQuery->orWhere(function ($organizationQuery) use ($request) {
                                $organizationQuery
                                ->orWhere('organisation_name', 'like', '%' . $request->input('search') . '%');
                            });
                        });
                    }

                    $query->where(
                        'publication_status',
                        config("constants.publication_status")["APPROVED"]
                    );

                    if ($request->has('country_id') && $request->input('country_id') !== '') {
                        $query->where("mission.country_id", $request->input('country_id'));
                    }
                    if ($request->has('state_id') && $request->input('state_id') !== '') {
                        $query->whereIn("mission.state_id", explode(",", $request->input('state_id')));
                    }
                    if ($request->has('city_id') && $request->input('city_id') !== '') {
                        $query->whereIn("mission.city_id", explode(",", $request->input('city_id')));
                    }
                    if ($request->has('theme_id') && $request->input('theme_id') !== '') {
                        $query->whereIn("mission.theme_id", explode(",", $request->input('theme_id')));
                    }

                    if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
                        if ($request->input('explore_mission_type') === config('constants.THEME')) {
                            $query->where("mission.theme_id", $request->input('explore_mission_params'));
                        }
                        if ($request->input('explore_mission_type') === config('constants.COUNTRY')) {
                            $query->where(function ($query) use ($request) {
                                $query->wherehas('country', function ($countryQuery) use ($request) {
                                    $countryQuery->where(
                                        "mission.country_id",
                                        $request->input('explore_mission_params')
                                    );
                                });
                            });
                        }
                        if ($request->input('explore_mission_type') === config('constants.ORGANIZATION')) {
                            $query->where(
                                'organisation_name',
                                'like',
                                '%' . $request->input('explore_mission_params') . '%'
                            );
                        }
                    }
                });

                $missionSkillQuery->with('mission', 'skill');
                $missionSkillQuery->groupBy('skill_id');
                $missionSkillQuery->orderBy('mission_count', 'desc');
                $mission = $missionSkillQuery->get();
                break;

            case config('constants.STATE'):
                $stateMissionQuery = $this->modelsService->mission->select('*')->where(
                    'publication_status',
                    config("constants.publication_status")["APPROVED"]
                );
                if ($request->has('search') && $request->input('search') !== '') {
                    $stateMissionQuery->where(function ($query) use ($request) {
                        $query->with('missionLanguage');
                        $query->wherehas('missionLanguage', function ($missionLanguageQuery) use ($request) {
                            $missionLanguageQuery->where('title', 'like', '%' . $request->input('search') . '%');
                            $missionLanguageQuery
                            ->orWhere('short_description', 'like', '%' . $request->input('search') . '%');
                        });
                        $query->orWhere(function ($organizationQuery) use ($request) {
                            $organizationQuery
                            ->orWhere('organisation_name', 'like', '%' . $request->input('search') . '%');
                        });
                    });
                }
                if ($request->has('explore_mission_type') && $request->input('explore_mission_type') !== '') {
                    if ($request->input('explore_mission_type') === config('constants.THEME')) {
                        $stateMissionQuery->where("mission.theme_id", $request->input('explore_mission_params'));
                    }
                    if ($request->input('explore_mission_type') === config('constants.COUNTRY')) {
                        $stateMissionQuery->where(function ($query) use ($request) {
                            $query->wherehas('country', function ($countryQuery) use ($request) {
                                $countryQuery->where("mission.country_id", $request->input('explore_mission_params'));
                            });
                        });
                    }
                    if ($request->input('explore_mission_type') === config('constants.ORGANIZATION')) {
                        $stateMissionQuery->where(
                            'organisation_name',
                            'like',
                            '%' . $request->input('explore_mission_params') . '%'
                        );
                    }
                }

                $stateMissionQuery->with(['state'=> function ($query) {
                    $query->with('languages');
                }])
                ->selectRaw('COUNT(mission.mission_id) as mission_count');
                if ($request->has('country_id') && $request->input('country_id') !== '') {
                    $stateMissionQuery->where("mission.country_id", $request->input('country_id'));
                }
                $stateMissionQuery->groupBy('mission.state_id');
                $mission = $stateMissionQuery->get();
                break;
        }
        return $mission;
    }

    /**
     * Add/remove mission to favourite.
     *
     * @param int $userId
     * @param int $missionId
     * @return null|App\Models\FavouriteMission
     */
    public function missionFavourite(int $userId, int $missionId): ?FavouriteMission
    {
        $mission = $this->modelsService->mission->findOrFail($missionId);
        $favouriteMission = $this->modelsService->favouriteMission->findFavourite($userId, $missionId);

        if (is_null($favouriteMission)) {
            $favouriteMissions = $this->modelsService->favouriteMission->addToFavourite($userId, $missionId);
        } else {
            $favouriteMissions =  $favouriteMission->removeFromFavourite($userId, $missionId);
        }
        return $this->modelsService->favouriteMission->findFavourite($userId, $missionId);
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
        return $this->modelsService->missionRating->createOrUpdateRating(['mission_id' => $request['mission_id'],
        'user_id' => $userId], $missionRating);
    }

    /**
     * Display listing of related mission.
     *
     * @param Illuminate\Http\Request $request
     * @param int missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedMissions(Request $request, int $missionId): Collection
    {
        // Check mission id exists or not
        $mission = $this->modelsService->mission->findOrFail($missionId);
        $relatedCityCount = $this->modelsService->mission->where('city_id', $mission->city_id)
        ->whereNotIn('mission.mission_id', [$missionId])->count();

        $relatedCountryCount = $this->modelsService->mission->where('country_id', $mission->country_id)
        ->whereNotIn('mission.mission_id', [$missionId])->count();

        // Get mission data
        $missionQuery = $this->modelsService->mission->whereNotIn('mission.mission_id', [$missionId])
        ->select('mission.*')->take(config("constants.RELATED_MISSION_LIMIT"));

        $missionQuery = ($relatedCityCount > 0) ? $missionQuery->where('city_id', $mission->city_id)
        : (($relatedCityCount === 0) && ($relatedCountryCount > 0))
        ? $missionQuery->where('country_id', $mission->country_id)
        : $missionQuery->where('theme_id', $mission->theme_id);

        $missionQuery->where('publication_status', config("constants.publication_status")["APPROVED"])
        ->with(['missionTheme', 'missionMedia', 'goalMission', 'timeMission'
        ])->with(['missionMedia' => function ($query) {
            $query->where('status', '1');
            $query->where('default', '1');
        }])
        ->with(['missionLanguage' => function ($query) {
            $query->select(
                'mission_language_id',
                'mission_id',
                'language_id',
                'title',
                'short_description',
                'objective',
                'label_goal_achieved',
                'label_goal_objective'
            );
        }])
        ->with(['city.languages'])
        ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
            $query->where('user_id', $request->auth->user_id)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
            config("constants.application_status")["PENDING"]])->whereNull('deleted_at');
        }])
        ->withCount(['missionApplication as mission_application_count' => function ($query) {
            $query->whereIn('approval_status', [
            config("constants.application_status")["AUTOMATICALLY_APPROVED"]])->whereNull('deleted_at');
        }])
        ->withCount(['favouriteMission as favourite_mission_count' => function ($query) use ($request) {
            $query->where('user_id', $request->auth->user_id);
        }])
        ->whereNotIn('mission.mission_id', function ($query) use ($request) {
            $query->select('mission_id')
                ->from('mission_application')
                ->where('user_id', $request->auth->user_id)
                ->where('approval_status', '<>', config("constants.application_status")["REFUSED"])
                ->whereNull('deleted_at');
        });
        $missionQuery->withCount([
            'missionRating as mission_rating_count' => function ($query) {
                $query->select(DB::raw("AVG(rating) as rating"));
            }
        ]);
        $missionQuery->withCount([
            'timesheet AS achieved_goal' => function ($query) use ($request) {
                $query->select(DB::raw("SUM(action) as action"));
                $query->whereIn('status', array(config('constants.timesheet_status.APPROVED'),
                config('constants.timesheet_status.AUTOMATICALLY_APPROVED')));
            }]);
        $missionQuery->with(['missionRating']);
        return $missionQuery->inRandomOrder()->get();
    }

    /**
     * Get mission detail
     *
     * @param Illuminate\Http\Request $request
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getMissionDetail(Request $request, int $missionId): Collection
    {
        $mission = $this->modelsService->mission->findOrFail($missionId);
        // Get  mission detail
        $missionQuery = $this->modelsService->mission->select('mission.*')->where('mission_id', $missionId);
        $missionQuery->where('publication_status', config("constants.publication_status")["APPROVED"])
            ->with(['missionTheme', 'missionMedia', 'goalMission', 'timeMission', 'availability'])
            ->with(['missionSkill' => function ($query) {
                $query->with('mission', 'skill');
            }])
            ->with(['missionApplication' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->where('approval_status', '<>', config("constants.application_status")["REFUSED"]);
            }])
            ->with(['missionMedia' => function ($query) {
                $query->where('status', '1');
                $query->where('default', '1');
                $query->orderBy('sort_order');
            }])
            ->with(['missionDocument' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->with(['missionRating'  => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id);
            }])
            ->with(['favouriteMission'  => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id);
            }])
            ->with(['missionLanguage' => function ($query) {
                $query->select(
                    'mission_language_id',
                    'mission_id',
                    'language_id',
                    'title',
                    'short_description',
                    'objective',
                    'description',
                    'custom_information',
                    'label_goal_achieved',
                    'label_goal_objective'
                );
            }])
            ->with(['city.languages'])
            ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"],
                config("constants.application_status")["PENDING"]]);
            }])
            ->withCount(['missionApplication as mission_application_count' => function ($query) use ($request) {
                $query->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
            }])
            ->withCount(['favouriteMission as favourite_mission_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id);
            }])
            ->withCount([
                'missionRating as mission_rating_count' => function ($query) {
                    $query->select(DB::raw("AVG(rating) as rating"));
                }
            ])->withCount([
                'missionRating as mission_rating_total_volunteers'
            ]);
            
        $missionQuery->withCount([
                'timesheet AS achieved_goal' => function ($query) use ($request) {
                    $query->select(DB::raw("SUM(action) as action"));
                    $query->whereIn('status', array(config('constants.timesheet_status.APPROVED'),
                    config('constants.timesheet_status.AUTOMATICALLY_APPROVED')));
                }]);
        return $missionQuery->get();
    }

    /**
     * Display mission media.
     *
     * @param int $missionId
     * @return Illuminate\Database\Eloquent\Collection
    */
    public function getMissionMedia(int $missionId): Collection
    {
        // Fetch mission media details
        $missionData = $this->modelsService->mission->findOrFail($missionId);
        return $missionData->missionMedia()->orderBy('sort_order')
        ->take(config("constants.MISSION_MEDIA_LIMIT"))->get();
    }
        
    /**
     * Check seats are available or not.
     *
     * @param int $missionId
     * @return bool
     */
    public function checkAvailableSeats(int $missionId): bool
    {
        $mission = $this->modelsService->mission->checkAvailableSeats($missionId);
        if ($mission['total_seats'] !== null) {
            $seatsLeft = $mission['total_seats'] - $mission['mission_application_count'];
            return $seatsLeft > 0;
        }
        return true;
    }
    
    /**
     * Check mission application deadline
     *
     * @param int $missionId
     * @return bool
     */
    public function checkMissionApplicationDeadline(int $missionId): bool
    {
        $mission = $this->modelsService->mission->findOrFail($missionId);
        $applicationStatus = true;
        if ($mission->mission_type === config('constants.mission_type.TIME')) {
            $applicationDeadline = $this->modelsService->timeMission->getApplicationDeadLine($missionId);
            $applicationStatus = (is_null($applicationDeadline) || $applicationDeadline > Carbon::now()) ? true : false;
            
            $timeMissionDetails = $this->modelsService->timeMission->getTimeMissionDetails($missionId)->toArray();
            $todayDate = Carbon::parse(date(config("constants.DB_DATE_FORMAT")));
            $today = $todayDate->setTimezone(config('constants.TIMEZONE'))->format(config('constants.DB_DATE_FORMAT'));
            $todayTime = $this->helpers->getUserTimeZoneDate(date(config("constants.DB_DATE_TIME_FORMAT")));
             
            if ((!isset($timeMissionDetails[0]['application_deadline'])) && ((isset($timeMissionDetails[0]['application_start_date']) && ($timeMissionDetails[0]['application_start_date'] !== null)) &&
            (isset($timeMissionDetails[0]['application_end_date']) && ($timeMissionDetails[0]['application_end_date'] !== null)) &&
            ($timeMissionDetails[0]['application_end_date'] < $today || $timeMissionDetails[0]['application_start_date'] > $today))) {
                $applicationStatus = false;
            }

            if ((isset($timeMissionDetails[0]['application_start_time']) && ($timeMissionDetails[0]['application_start_time'] !== null)) &&
            (isset($timeMissionDetails[0]['application_end_time']) && ($timeMissionDetails[0]['application_end_time'] !== null)) &&
            ($timeMissionDetails[0]['application_end_time'] < $todayTime || $timeMissionDetails[0]['application_start_time'] > $todayTime)) {
                $applicationStatus = false;
            }
        }
        return $applicationStatus;
    }

    /**
     * Get mission details from mission id and language id
     *
     * @param int $missionId
     * @param int $langId
     * @return App\Models\Mission|null
     */
    public function getMissionDetailsFromId(int $missionId, int $langId): ?Mission
    {
        $mission = $this->modelsService->mission->where('mission_id', $missionId)
        ->with(
            [
                'missionLanguage' => function ($q) use ($langId) {
                    $q->where('language_id', $langId);
                },
                'missionMedia' => function ($q) {
                    $q->where('default', '1');
                }
            ]
        )
        ->first();
        return $mission;
    }

    /** Get mission application details by mission id, user id and status
     *
     * @param int $missionId
     * @param int $userId
     * @param string $status
     * @return App\Models\MissionApplication
     */
    public function getMissionApplication(int $missionId, int $userId, string $status): MissionApplication
    {
        return $this->modelsService->missionApplication->where(['user_id' => $userId,
                'mission_id' => $missionId, 'approval_status' => $status])
                ->firstOrFail();
    }

    /**
     * Get Mission data for timesheet
     *
     * @param int $id
     * @return App\Models\Mission
     */
    public function getTimesheetMissionData(int $id): Mission
    {
        return $this->modelsService->mission->with('goalMission')
        ->select('mission_id', 'start_date', 'end_date', 'mission_type', 'city_id')
        ->findOrFail($id);
    }
    
    /**
     * Get Mission type
     *
     * @param int $id
     * @return null|Collection
     */
    public function getMissionType(int $id): ?Collection
    {
        return $this->modelsService->mission->select('mission_type', 'city_id')
        ->where('mission_id', $id)
        ->get();
    }

    /**
     * Get user mission lists
     *
     * @param Illuminate\Http\Request $request
     * @return null|array
     */
    public function getUserMissions(Request $request): ?array
    {
        $languageId = $this->languageHelper->getLanguageId($request);
        $defaultTenantLanguage = $this->languageHelper->getDefaultTenantLanguage($request);
        $defaultTenantLanguageId = $defaultTenantLanguage->language_id;
        $userId = $request->auth->user_id;
        $missionLists = array();

        $missionData = $this->modelsService->mission->select('mission.mission_id', 'city_id')
        ->whereHas('missionApplication', function ($query) use ($userId) {
            $query->where('user_id', $userId)
            ->whereIn('approval_status', [config("constants.application_status")["AUTOMATICALLY_APPROVED"]]);
        })
        ->with(['missionLanguage' => function ($query) use ($languageId) {
            $query->select('mission_language_id', 'mission_id', 'title', 'language_id');
        }])->get();
        
        foreach ($missionData as $key => $value) {
            $index = array_search($languageId, array_column($value->missionLanguage->toArray(), 'language_id'));
            $language = ($index === false) ? $defaultTenantLanguageId : $languageId;
            $missionLanguage = $value->missionLanguage->where('language_id', $language)->first();
            
            $missionLists[$key]['title'] = $missionLanguage->title ?? '';
            $missionLists[$key]['mission_id'] = $value->mission_id;
        }
        return $missionLists;
    }

    /** Get mission title
     *
     * @param int $missionId
     * @param int $languageId
     * @param int $defaultTenantLanguageId
     * @return string
     */
    public function getMissionTitle(int $missionId, int $languageId, int $defaultTenantLanguageId): string
    {
        $languageData = $this->modelsService->missionLanguage->withTrashed()->select('title', 'language_id')
        ->where(['mission_id' => $missionId])
        ->get();
        $missionTitle = '';
        if ($languageData->count() > 0) {
            $index = array_search($languageId, array_column($languageData->toArray(), 'language_id'));
            $language = ($index === false) ? $defaultTenantLanguageId : $languageId;
            $missionLanguage = $languageData->where('language_id', $language)->first();
            $missionTitle =  $missionLanguage->title ?? '';
        }
        return $missionTitle;
    }

    /**
     * Check user has any relation with mission or not, based on availability or skill
     * @param int $missionId
     * @param int $userId
     * @return int
     */
    public function checkIsMissionRelatedToUser(int $missionId, int $userId): int
    {
        return $this->modelsService->mission
            ->where('mission_id', $missionId)
            ->where(function ($query) use ($userId) {
                $query->whereHas('missionSkill.skilledUsers', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
                $query->OrWhereHas('availableUsers', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->count();
    }
    
    /**
     * Check mission status
     *
     * @param int $missionId
     * @return bool
     */
    public function checkMissionStatus(int $missionId): bool
    {
        $mission = $this->modelsService->mission->select('publication_status')
        ->where('mission_id', $missionId)->get();
        $missionStatus = array(
            config('constants.publication_status.APPROVED'),
            config('constants.publication_status.PUBLISHED_FOR_APPLYING')
        );
        if (isset($mission[0]['publication_status'])
        && (in_array($mission[0]['publication_status'], $missionStatus))) {
            $status = true;
        }
        return $status ?? false;
    }

    /**
     * Remove mission media
     *
     * @param int $mediaId
     *
     * @return bool
     */
    public function deleteMissionMedia(int $mediaId): bool
    {
        return $this->missionMediaRepository->deleteMedia($mediaId);
    }

    /**
     * Remove mission document
     *
     * @param int $documentId
     *
     * @return bool
     */
    public function deleteMissionDocument(int $documentId): bool
    {
        return $this->modelsService->missionDocument->deleteDocument($documentId);
    }
    
    /**
     * Get media details
     *
     * @param int $mediaId
     *
     * @return Collection
     */
    public function getMediaDetails(int $mediaId): Collection
    {
        return $this->missionMediaRepository->getMediaDetails($mediaId);
    }

    /**
     * Get mission media details
     *
     * @param int $documentId
     * @return App\Models\MissionDocument
     */
    public function findDocument(int $documentId): MissionDocument
    {
        return $this->modelsService->missionDocument->findOrFail($documentId);
    }

    /**
     * Check document is linked with mission or not
     *
     * @param int $documentId
     * @return bool
     */
    public function isDocumentLinkedToMission(int $documentId, int $missionId): bool
    {
        $document = $this->modelsService->missionDocument
        ->where(['mission_document_id' => $documentId, 'mission_id' => $missionId])
        ->first();
        return ($document === null) ? false : true;
    }
    
    /**
     * Check mission user mission application status
     *
     * @param int $missionId
     * @param int $userId
     * @param array $statusArray
     *
     * @return bool
     */
    public function checkUserMissionApplicationStatus(int $missionId, int $userId, array $statusArray): bool
    {
        $applicationStatusData = $this->modelsService->missionApplication->select('approval_status')
        ->where(['mission_id'=> $missionId, 'user_id'=> $userId])
        ->whereIn('approval_status', $statusArray)->get();
        return $applicationStatusData->isEmpty() ? true : false;
    }
}
