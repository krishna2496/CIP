<?php

namespace App\Repositories\Mission;

use App\Repositories\Mission\MissionInterface;
use App\Repositories\UserFilter\UserFilterRepository;
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
use App\Models\MissionApplication;
use App\Models\UserFilter;
use Validator;
use PDOException;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class MissionRepository implements MissionInterface
{
    /**
     * @var App\Models\Mission
     */
    public $mission;

    /**
     * @var App\Models\MissionApplication
     */
    public $missionApplication;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Repositories\UserFilter\UserFilterRepository
     */
    private $userFilterRepository;

    /**
     * @var App\Models\UserFilter
     */
    public $userFilter;
    
    /*
     * @var App\Helpers\LanguageHelper
     */

    private $languageHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new Mission repository instance.
     *
     * @param  App\Models\Mission $mission
     * @param  App\Models\MissionApplication $missionApplication
     * @param  App\Models\MissionLanguage $missionLanguage
     * @param  App\Models\MissionMedia $missionMedia
     * @param  App\Models\MissionDocument $missionDocument
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @param  Illuminate\Http\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        Mission $mission,
        MissionApplication $missionApplication,
        MissionLanguage $missionLanguage,
        MissionMedia $missionMedia,
        MissionDocument $missionDocument,
        ResponseHelper $responseHelper,
        UserFilterRepository $userFilterRepository,
        UserFilter $userFilter,
        LanguageHelper $languageHelper,
        Helpers $helpers
    ) {
        $this->mission = $mission;
        $this->missionLanguage = $missionLanguage;
        $this->missionMedia = $missionMedia;
        $this->missionDocument = $missionDocument;
        $this->missionApplication = $missionApplication;
        $this->responseHelper = $responseHelper;
        $this->userFilterRepository = $userFilterRepository;
        $this->userFilter = $userFilter;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
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

        // Set data for create new record
        $startDate = $endDate = null;
        if (isset($request->start_date)) {
            $startDate = ($request->start_date != '') ?
             Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : null;
        }
        if (isset($request->end_date)) {
            $endDate = ($request->end_date != '') ?
             Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : null;
        }
        $applicationDeadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ?
         Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : null;

        $countryId = $this->helpers->getCountryId($request->location['country_code']);
        $missionData = array(
                'theme_id' => $request->theme_id,
                'city_id' => $request->location['city_id'],
                'country_id' => $countryId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ?
                 $request->total_seats : null,
                'application_deadline' => $applicationDeadline,
                'publication_status' => $request->publication_status,
                'organisation_id' => $request->organisation['organisation_id'],
                'organisation_name' => $request->organisation['organisation_name'],
                'mission_type' => $request->mission_type,
                'goal_objective' => $request->goal_objective
            );
        
        // Create new record
        $mission = $this->mission->create($missionData);

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

        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $isDefault = 0;

        // Add mission media images
        foreach ($request->media_images as $value) {
            $filePath = S3Helper::uploadFileOnS3Bucket($value['media_path'], $tenantName);
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

        // Add mission media videos
        foreach ($request->media_videos as $value) {
            $missionMedia = array('mission_id' => $mission->mission_id,
                                  'media_name' => $value['media_name'],
                                  'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                  'media_path' => $value['media_path']);
            $this->missionMedia->create($missionMedia);
            unset($missionMedia);
        }

        // Add mission documents
        foreach ($request->documents as $value) {
            $filePath = S3Helper::uploadFileOnS3Bucket($value['document_path'], $tenantName);
            $missionDocument = array('mission_id' => $mission->mission_id,
                                    'document_name' => $value['document_name'],
                                    'document_type' => pathinfo($value['document_name'], PATHINFO_EXTENSION),
                                    'document_path' => $filePath);
            $this->missionDocument->create($missionDocument);
            unset($missionDocument);
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
        $startDate = $endDate = null;
        if (isset($request->start_date)) {
            $startDate = ($request->start_date != '') ?
             Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : null;
        }
        if (isset($request->end_date)) {
            $endDate = ($request->end_date != '') ?
             Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : null;
        }
        $applicationDeadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ?
         Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : null;

        $countryId = $this->helpers->getCountryId($request->location['country_code']);
        $missionData = array('theme_id' => $request->theme_id,
                             'city_id' => $request->location['city_id'],
                             'country_id' => $countryId,
                             'start_date' => $startDate,
                             'end_date' => $endDate,
                             'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ?
                              $request->total_seats : null,
                             'application_deadline' => $applicationDeadline,
                             'publication_status' => $request->publication_status,
                             'organisation_id' => $request->organisation['organisation_id'],
                             'organisation_name' => $request->organisation['organisation_name'],
                             'mission_type' => $request->mission_type,
                             'goal_objective' => $request->goal_objective);
        
        // Update record
        $mission = $this->mission->findOrFail($id);
        $mission->update($missionData);
       
        // Add/Update mission title
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

        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        // Add/Update  mission media images
        $isDefault = 0;
        foreach ($request->media_images as $value) {
            $filePath = S3Helper::uploadFileOnS3Bucket($value['media_path'], $tenantName);
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

        // Add/Update mission media videos
        foreach ($request->media_videos as $value) {
            $missionMedia = array('mission_id' => $id,
                                  'media_name' => $value['media_name'],
                                  'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                  'media_path' => $value['media_path']);

            $this->missionMedia->createOrUpdateMedia(['mission_id' => $id,
             'mission_media_id' => $value['media_id']], $missionMedia);
            unset($missionMedia);
        }

        // Add/Update mission documents
        foreach ($request->documents as $value) {
            $missionDocument = array('mission_id' => $id,
                                    'document_name' => $value['document_name'],
                                    'document_type' => pathinfo($value['document_name'], PATHINFO_EXTENSION)
                                  );
            if ($value['document_path'] != '') {
                $filePath = S3Helper::uploadFileOnS3Bucket($value['document_path'], $tenantName);
                $missionDocument['document_path'] = $filePath;
            }
            
            $this->missionDocument->createOrUpdateDocument(['mission_id' => $id,
             'mission_document_id' => $value['document_id']], $missionDocument);
            unset($missionDocument);
        }
        return $mission;
    }
    
    /**
     * Find the specified resource from database
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
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
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $mission_id
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function missionApplications(Request $request, int $missionId): LengthAwarePaginator
    {
        $missionApplicationDetails = $this->missionApplication->find($request, $missionId);
        return $missionApplicationDetails;
    }

    /**
     * Display specified resource.
     *
     * @param int $missionId
     * @param int $applicationId
     * @return \Illuminate\Http\Response
     */
    public function missionApplication(int $missionId, int $applicationId)
    {
        $missionApplicationDetail = $this->missionApplication->findDetail($missionId, $applicationId);
        return $missionApplicationDetail;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $missionId
     * @param int $applicationId
     * @return \Illuminate\Http\Response
     */
    public function updateApplication(Request $request, int $missionId, int $applicationId)
    {
        $missionApplication = $this->missionApplication->findOrFail($applicationId);
        $missionApplication->update($request->toArray());
        
        return $missionApplication;
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
            'mission.goal_objective',
            'mission.end_date',
            'mission.total_seats',
            'mission.mission_type',
            'mission.goal_objective',
            'mission.application_deadline',
            'mission.publication_status',
            'mission.organisation_id',
            'mission.organisation_name'
        )
        ->with(['city', 'country', 'missionTheme', 'missionLanguage', 'missionMedia', 'missionDocument'])
        ->withCount('missionApplication')
        ->orderBy('mission.mission_id', 'ASC')->paginate(config('constants.PER_PAGE_LIMIT'));

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
     * Illuminate\Http\Request $request'
     * Array $userFilterData
     * @return mixed
     */
    public function appMissions(Request $request, array $userFilterData, int $languageId)
    {
        $missionData = [];

        // Get  mission data
        $missionQuery = $this->mission->select(
            'mission.mission_id',
            'mission.theme_id',
            'mission.city_id',
            'mission.country_id',
            'mission.start_date',
            'mission.end_date',
            'mission.total_seats',
            'mission.mission_type',
            'mission.goal_objective',
            'mission.end_date',
            'mission.total_seats',
            'mission.mission_type',
            'mission.goal_objective',
            'mission.application_deadline',
            'mission.publication_status',
            'mission.organisation_id',
            'mission.organisation_name'
        )
            ->with(['missionTheme', 'missionMedia'
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
                ->where('approval_status', config("constants.application_status")["AUTOMATICALLY_APPROVED"]);
            }])
            ->withCount(['missionApplication as mission_application_count' => function ($query) use ($request) {
                $query->where('approval_status', config("constants.application_status")["AUTOMATICALLY_APPROVED"]);
            }]);
            

        if ($userFilterData['search'] && $userFilterData['search'] != '') {
            $missionQuery->wherehas('missionLanguage', function ($q) use ($userFilterData) {
                $q->Where('title', 'like', '%' . $userFilterData['search'] . '%');
                $q->orWhere('short_description', 'like', '%' . $userFilterData['search'] . '%');
            });
            $missionQuery->orWhere(function ($qry) use ($userFilterData) {
                $qry->orWhere('organisation_name', 'like', '%' . $userFilterData['search'] . '%');
            });
        }

        $missionQuery->where('publication_status', config("constants.publication_status")["APPROVED"]);

        $mission =  $missionQuery->orderBy('mission.mission_id', 'ASC')->paginate(config("constants.PER_PAGE_LIMIT"));

        return $mission;
    }

    /**
     * Display a top mission data.
     *
     * Illuminate\Http\Request $request
     * string $topFilterData
     * @return mixed
     */
    public function topMission(Request $request, string $topFilterParams)
    {
        // Get  mission data
        $missionQuery = $this->mission->select('*')
        ->where('publication_status', config("constants.publication_status")["APPROVED"]);
        if ($topFilterParams == config('constants.TOP_THEME')) {
            $missionQuery
            ->selectRaw('COUNT(mission.theme_id) as mission_theme_count')
            ->with(['missionTheme'])
            ->groupBy('mission.theme_id')
            ->orderBY('mission_theme_count', 'desc');
        }
        if ($topFilterParams == config('constants.TOP_COUNTRY')) {
            $missionQuery->with(['country'])
            ->selectRaw('COUNT(mission.country_id) as mission_country_count')
            ->groupBy('mission.country_id')
            ->orderBY('mission_country_count', 'desc');
        }
        $mission = $missionQuery->limit(5)->get();
        return $mission;
    }
}
