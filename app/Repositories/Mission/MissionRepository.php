<?php
namespace App\Repositories\Mission;

use App\Repositories\Mission\MissionInterface;
use Illuminate\Http\{Request, Response};
use App\Helpers\{Helpers, ResponseHelper};
use App\Models\Mission;
use App\Models\MissionApplication;
use App\Models\MissionLanguage;
use App\Models\MissionMedia;
use App\Models\MissionDocument;
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
     * @param  App\Mission $mission
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(
        Mission $mission, 
        MissionApplication $missionApplication, 
        MissionLanguage $missionLanguage,
        MissionMedia $missionMedia,
        Response $response)
    {
        $this->mission = $mission;
        $this->response = $response;
        $this->missionApplication = $missionApplication;
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        // Set data for create new record
        $startDate = $endDate = NULL;
        if (isset($request->start_date))
            $startDate = ($request->start_date != '') ? Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
        if (isset($request->end_date))
            $endDate = ($request->end_date != '') ? Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
        $application_deadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ? Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : NULL;

        $country_id = Helpers::getCountryId($request->location['country_code']);
        $missionData = array('theme_id' => $request->theme_id, 
                             'city_id' => $request->location['city_id'], 
                             'country_id' => $country_id, 
                             'start_date' => $startDate, 
                             'end_date' => $endDate, 
                             'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ? $request->total_seats : NULL, 
                             'application_deadline' => $application_deadline, 
                             'publication_status' => $request->publication_status, 
                             'organisation_id' => $request->organisation['organisation_id'], 
                             'organisation_name' => $request->organisation['organisation_name'],
                             'mission_type' => $request->mission_type,
                             'goal_objective' => $request->goal_objective);
        
        // Create new record 
        $mission = $this->mission->create($missionData);

        // Add mission title 
        foreach ($request->mission_detail as $value) {              
            $language = $languages->where('code', $value['lang'])->first();
            $missionLanguage = array('mission_id' => $mission->mission_id, 
                                    'language_id' => $language->language_id, 
                                    'title' => $value['title'], 
                                    'short_description' => (isset($value['short_description'])) ? $value['short_description'] : NULL, 
                                    'description' => (array_key_exists('section', $value)) ? serialize($value['section']) : '',
                                    'objective' => $value['objective']);
            MissionLanguage::create($missionLanguage);
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
                MissionMedia::where('mission_id', $mission->mission_id)->update($media);
            }
            
            $missionMedia = array('mission_id' => $mission->mission_id, 
                                  'media_name' => $value['media_name'], 
                                  'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION), 
                                  'media_path' => $filePath,
                                  'default' => $default);
            MissionMedia::create($missionMedia);
            unset($missionMedia);
        }

        if ($isDefault == 0) {
            $mediaData = MissionMedia::where('mission_id', $mission->mission_id)->orderBy('mission_media_id', 'ASC')->first();
            $missionMedia = array('default' => '1');
            MissionMedia::where('mission_media_id', $mediaData->mission_media_id)->update($missionMedia);
        }

        // Add mission media videos
        foreach ($request->media_videos as $value) {                    
            
            $missionMedia = array('mission_id' => $mission->mission_id, 
                                  'media_name' => $value['media_name'], 
                                  'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                  'media_path' => $value['media_path']);
            MissionMedia::create($missionMedia);
            unset($missionMedia);
        }

        // Add mission documents 
        foreach ($request->documents as $value) {                    
            
            $filePath = Helpers::uploadFileOnS3Bucket($value['document_path'], $tenantName); 
            $missionDocument = array('mission_id' => $mission->mission_id, 
                                    'document_name' => $value['document_name'], 
                                    'document_type' => pathinfo($value['document_name'], PATHINFO_EXTENSION),
                                    'document_path' => $filePath);
            MissionDocument::create($missionDocument);
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
    public function update(Request $request, int $id)
    {
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
        $missionApplications = $this->missionApplication->find($request, $missionId);   
        return $missionApplications;
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
        $missionApplication = $this->missionApplication->findDetail($missionId, $applicationId);   
        return $missionApplication;
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
    

}
