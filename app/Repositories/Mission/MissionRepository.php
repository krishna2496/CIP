<?php
namespace App\Repositories\Mission;

use App\Repositories\Mission\MissionInterface;
use Illuminate\Http\{Request, Response};
use App\Helpers\{Helpers, ResponseHelper, LanguageHelper};
use App\Models\{Mission, MissionLanguage, MissionDocument, MissionMedia, MissionApplication};
use Validator, PDOException, DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new Mission repository instance.
     *
     * @param  App\Models\Mission $mission
     * @param  App\Models\MissionApplication $missionApplication
     * @param  App\Models\MissionLanguage $missionLanguage
     * @param  App\Models\MissionMedia $missionMedia
     * @param  App\Models\MissionDocument $missionDocument
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(
        Mission $mission, 
        MissionApplication $missionApplication, 
        MissionLanguage $missionLanguage,
        MissionMedia $missionMedia,
        MissionDocument $missionDocument, 
        Response $response)
    {
        $this->mission = $mission;
        $this->missionLanguage = $missionLanguage;
        $this->missionMedia = $missionMedia;
        $this->missionDocument = $missionDocument;
        $this->missionApplication = $missionApplication;
        $this->response = $response;
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request): Mission
    {
        $languages = LanguageHelper::getLanguages($request);  

        // Set data for create new record
        $startDate = $endDate = NULL;
        if (isset($request->start_date))
            $startDate = ($request->start_date != '') ? Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
        if (isset($request->end_date))
            $endDate = ($request->end_date != '') ? Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
        $applicationDeadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ? Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : NULL;

        $countryId = Helpers::getCountryId($request->location['country_code']);
        $missionData = array(
                'theme_id' => $request->theme_id, 
                'city_id' => $request->location['city_id'], 
                'country_id' => $countryId, 
                'start_date' => $startDate, 
                'end_date' => $endDate, 
                'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ? $request->total_seats : NULL, 
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
                    'short_description' => (isset($value['short_description'])) ? $value['short_description'] : NULL, 
                    'description' => (array_key_exists('section', $value)) ? $value['section'] : '',
                    'objective' => $value['objective']
                );

            $this->missionLanguage->create($missionLanguage);
            unset($missionLanguage);
        }

        $tenantName = Helpers::getSubDomainFromRequest($request);
        $isDefault = 0;

        // Add mission media images
        foreach ($request->media_images as $value) {                    
            
            $filePath = Helpers::uploadFileOnS3Bucket($value['media_path'], $tenantName);  
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
            $mediaData = $this->missionMedia->where('mission_id', $mission->mission_id)->orderBy('mission_media_id', 'ASC')->first();
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
            
            $filePath = Helpers::uploadFileOnS3Bucket($value['document_path'], $tenantName); 
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): Mission
    {
        $languages = LanguageHelper::getLanguages($request);
        // Set data for update record
        $startDate = $endDate = NULL;
        if (isset($request->start_date))
            $startDate = ($request->start_date != '') ? Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
        if (isset($request->end_date))
            $endDate = ($request->end_date != '') ? Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
        $applicationDeadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ? Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : NULL;

        $countryId = Helpers::getCountryId($request->location['country_code']);
        $missionData = array('theme_id' => $request->theme_id, 
                             'city_id' => $request->location['city_id'], 
                             'country_id' => $countryId, 
                             'start_date' => $startDate, 
                             'end_date' => $endDate, 
                             'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ? $request->total_seats : NULL,
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
                                    'short_description' => (isset($value['short_description'])) ? $value['short_description'] : NULL,                                        'description' => serialize($value['section']),
                                    'objective' => $value['objective']);

            $languageData = $this->missionLanguage->where('mission_id', $id)
                                    ->where('language_id', $language->language_id)
                                    ->count();
            if ($languageData > 0)
                $this->missionLanguage->where('mission_id', $id)
                                      ->where('language_id', $language->language_id)
                                      ->update($missionLanguage);
            else
                $this->missionLanguage->create($missionLanguage);
                
            unset($missionLanguage);
        }

        $tenantName = Helpers::getSubDomainFromRequest($request);
        // Add/Update  mission media images
        $isDefault = 0;
        foreach ($request->media_images as $value) {  
            $filePath = Helpers::uploadFileOnS3Bucket($value['media_path'], $tenantName);  
            // Check for default image in mission_media
            $default = (isset($value['default']) && ($value['default'] != '')) ? $value['default'] : '0';
            if($default == '1') {
                $isDefault = 1;
                $media = array('default' => '0');
                $this->missionMedia->where('mission_id', $id)->update($media);
            }
            
            $missionMedia = array('mission_id' => $id, 
                                  'media_name' => $value['media_name'], 
                                  'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION), 
                                  'media_path' => $filePath,
                                  'default' => $default);
            
            $mediaData = $this->missionMedia->where('mission_id', $id)
                                    ->where('mission_media_id', $value['media_id'])
                                    ->count();
            if (!empty($mediaData))
                $this->missionMedia->where('mission_id', $id)
                                    ->where('mission_media_id', $value['media_id'])
                                    ->update($missionMedia);
            else
                $this->missionMedia->create($missionMedia);

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
            $mediaData = $this->missionMedia->where('mission_id', $id)
                                    ->where('mission_media_id', $value['media_id'])
                                    ->count();
            if ($mediaData > 0)
                $this->missionMedia->where('mission_id', $id)
                                    ->where('mission_media_id', $value['media_id'])
                                    ->update($missionMedia);
            else
                $this->missionMedia->create($missionMedia);

            unset($missionMedia);
        }

        // Add/Update mission documents 
        foreach ($request->documents as $value) { 
            $missionDocument = array('mission_id' => $id, 
                                    'document_name' => $value['document_name'], 
                                    'document_type' => pathinfo($value['document_name'], PATHINFO_EXTENSION)
                                  );
            if($value['document_path'] != '') {
                $filePath = Helpers::uploadFileOnS3Bucket($value['document_path'], $tenantName); 
                $missionDocument['document_path'] = $filePath;
            }

            $documentData = $this->missionDocument->where('mission_id', $id)
                ->where('mission_document_id', $value['document_id'])
                ->count();
            if ($documentData > 0)
                $this->missionDocument->where('mission_id', $id)
                ->where('mission_document_id', $value['document_id'])
                ->update($missionDocument);
            else
                $this->missionDocument->create($missionDocument);
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
     * @return \Illuminate\Http\Response
     */
    public function missionApplications(Request $request, int $missionId)
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
     * Illuminate\Http\Request $request
     * @return mixed
     */
    public function missionList(Request $request)
    {           
        $languages = LanguageHelper::getLanguages($request); 

        $mission = Mission::select('mission.mission_id', 'mission.theme_id', 'mission.city_id', 'mission.country_id', 'mission.start_date', 'mission.end_date', 'mission.total_seats', 'mission.mission_type', 'mission.goal_objective', 'mission.end_date', 'mission.total_seats', 
                'mission.mission_type', 'mission.goal_objective', 'mission.application_deadline', 
                'mission.publication_status', 'mission.organisation_id', 'mission.organisation_name'
            )
            ->with(['city', 'country', 'missionTheme', 'missionLanguage', 'missionMedia', 'missionDocument'])
            ->withCount('missionApplication')
            ->orderBy('mission.mission_id', 'ASC')->paginate(config('constants.LIMIT'));

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
     * Illuminate\Http\Request $request
     * @return mixed
     */
    public function appMissions(Request $request)
    {
        $languages = LanguageHelper::getLanguages($request); 
        $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $language = $languages->where('code', $local)->first();
        $language_id = $language->language_id;

        // Get data for parent table
        $mission = $this->mission->select(
            'mission.mission_id', 'mission.theme_id', 'mission.city_id', 'mission.country_id',
            'mission.start_date', 'mission.end_date', 'mission.total_seats', 'mission.mission_type',
            'mission.goal_objective', 'mission.end_date', 'mission.total_seats',
            'mission.mission_type', 'mission.goal_objective', 'mission.application_deadline',
            'mission.publication_status', 'mission.organisation_id', 'mission.organisation_name'
            )->with(['missionTheme', 'missionMedia'
            ])->with(['missionMedia' => function ($query) {
                $query->where('status', '1');
                $query->where('default', '1');
            }])
            ->with(['missionLanguage' => function ($query) use ($language_id) {
                $query->select('mission_language_id', 'mission_id', 'title', 'short_description', 'objective')
                ->where('language_id', $language_id);
            }])
            ->withCount(['missionApplication as user_application_count' => function ($query) use ($request) {
                $query->where('user_id', $request->auth->user_id)
                ->where('approval_status',config("constants.application_status")["AUTOMATICALLY_APPROVED"]);      
            }])
            ->withCount(['missionApplication as mission_application_count' => function ($query) use ($request) {
                $query->where('approval_status',config("constants.application_status")["AUTOMATICALLY_APPROVED"]);         
            }])
            ->where('publication_status',config("constants.publication_status")["APPROVED"])
            ->orderBy('mission.mission_id', 'ASC')->paginate(config("constants.PER_PAGE_LIMIT"));

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
            $today = date(config("constants.FRONT_DATE_FORMAT"));

            if (($value->user_application_count > 0) || 
                ($value->application_deadline != '' && $value->application_deadline < $today) || 
                ($value->total_seats != 0 && $value->total_seats == $value->mission_application_count) || 
                ($value->end_date != '' && $value->end_date < $today) 
                // || ($value->mission_type != 'GOAL' && $value->goal_objective ==  $today)
            ) {
                $value->set_view_detail = 1;
            } 
            //If media type is youtube link
            // default_media_type
        }
        return $mission;
    }
}
