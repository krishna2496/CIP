<?php

namespace App\Http\Controllers\Admin\Mission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Input, Config};
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Repositories\Mission\MissionRepository;
use App\Models\{Mission, MissionLanguage, MissionDocument, MissionMedia, MissionTheme, MissionApplication};
use App\Helpers\{Helpers, ResponseHelper};
use Validator, DB;

class MissionController extends Controller
{   
    /**
     * Display a listing of Mission.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        // Connect master database to get language details
        Helpers::switchDatabaseConnection('mysql', $request);
        $languages = DB::table('language')->get();    
    
        // Connect tenant database
        Helpers::switchDatabaseConnection('tenant', $request);
       
        try { 
            // Get mission
            $mission = Mission::select('mission.mission_id', 'mission.theme_id', 'mission.city_id', 'mission.country_id', 'mission.start_date', 'mission.end_date', 'mission.total_seats', 'mission.mission_type', 'mission.goal_objective', 'mission.end_date', 'mission.total_seats', 
                'mission.mission_type', 'mission.goal_objective', 'mission.application_deadline', 
                'mission.publication_status', 'mission.organisation_id', 'mission.organisation_name'
            )
            ->with(['city', 'country','missionTheme', 'missionLanguage', 'missionMedia', 'missionDocument'])
            ->withCount('missionApplication')
            ->orderBy('mission.mission_id', 'ASC')->paginate(config('constants.LIMIT'));

            if (empty($mission)) {
                // Set response data
                $apiStatus = app('Illuminate\Http\Response')->status();
                $apiMessage = trans('api_success_messages.success_message.MESSAGE_NO_DATA_FOUND');
                return Helpers::response($apiStatus, $apiMessage);
            }

            foreach ($mission as $key => $value) {

                foreach ($value->missionLanguage as $languageValue) {

                    $languageData = $languages->where('language_id', $languageValue->language_id)->first();
                    $languageValue->description = (@unserialize($languageValue->description) === false) ? $languageValue->description : unserialize($languageValue->description);
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

            // Set response data
            $apiData = $mission; 
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_MISSION_LIST_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage, $apiData);                  
        } catch(\Exception $e) {
            // Catch database exception
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_500'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_500'), 
                                        trans('api_error_messages.custom_error_code.ERROR_40018'), 
                                        trans('api_error_messages.custom_error_message.40018'));           
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {  
        // Connect master database to get language details
        Helpers::switchDatabaseConnection('mysql', $request);
        $languages = DB::table('language')->get();    
       
        // Connect tenant database
        Helpers::switchDatabaseConnection('tenant', $request);

        // Server side validataion for mission
        $validator = Validator::make($request->toArray(), [
                            "theme_id" => "required", 
                            "mission_detail" => "required", 
                            "mission_type" => ['required', Rule::in(config('constants.mission_type'))], 
                            "organisation" => "required", 
                            "publication_status" => ['required', Rule::in(config('constants.publication_status'))],
                            "location" => "required",
                            "goal_objective" => "required_if:mission_type,GOAL"]);
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20106'),
                                        $validator->errors()->first());
        } 

        // Server side validataion for location
        $validator = Validator::make($request->location, [
                            "city_id" => "required", 
                            "country_code" => "required"]);
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20106'),
                                        $validator->errors()->first());
        }  

        // Server side validataion for mission language
        foreach ($request->mission_detail as $value) {  
            $languageValidator = Validator::make($value, ["lang" => "required", "title" => "required" ]);
            if ($languageValidator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $languageValidator->errors()->first());
            } 
        }

        // Server side validataions for media images
        foreach ($request->media_images as $value) { 
            $validator = Validator::make($value, ["media_name" => "required", 
                                                  "media_type" => [Rule::in(config('constants.image_types'))], 
                                                  "media_path" => "required"]);
            if ($validator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $validator->errors()->first());
            }  
        }

        // Server side validataion for media videos
        foreach ($request->media_videos as $value) { 
            $validator = Validator::make($value, ["media_name" => "required", 
                                                  "media_path" => "required" ]);
            if ($validator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $validator->errors()->first());
            }
        }

        // Server side validataion for documents
        foreach ($request->documents as $value) {  
            $validator = Validator::make($value, ["document_name" => "required", 
                                                  "document_type" => [Rule::in(config('constants.document_types'))],
                                                  "document_path" => "required" ]);
            if ($validator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $validator->errors()->first());
            }   
        }
        try { 
            // Set data for create new record
            $start_date = $end_date = NULL;
            if (isset($request->start_date))
                $start_date = ($request->start_date != '') ? Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
            if (isset($request->end_date))
                $end_date = ($request->end_date != '') ? Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
            $application_deadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ? Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : NULL;

            $country_id = Helpers::getCountryId($request->location['country_code']);
            $missionData = array('theme_id' => $request->theme_id, 
                                 'city_id' => $request->location['city_id'], 
                                 'country_id' => $country_id, 
                                 'start_date' => $start_date, 
                                 'end_date' => $end_date, 
                                 'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ? $request->total_seats : NULL, 
                                 'application_deadline' => $application_deadline, 
                                 'publication_status' => $request->publication_status, 
                                 'organisation_id' => $request->organisation['organisation_id'], 
                                 'organisation_name' => $request->organisation['organisation_name'],
                                 'mission_type' => $request->mission_type,
                                 'goal_objective' => $request->goal_objective);
            
            // Create new record 
            $mission = Mission::create($missionData);
			
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
           
            // Set response data
            $apiStatus = trans('api_success_messages.status_code.HTTP_STATUS_201');
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_MISSION_ADD_SUCCESS');
            $apiData = ['mission_id' => $mission->mission_id];
            return Helpers::response($apiStatus, $apiMessage, $apiData);

        } catch (\Exception $e) {
			// Any other error occured when trying to insert data into database.
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'), 
                                    trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'), 
                                    trans('api_error_messages.custom_error_code.ERROR_20004'), 
                                    trans('api_error_messages.custom_error_message.20004'));
            
        }
    }

    /**
     * Display the specified mission detail.
     *
     * @param int $id
     * @return mixed
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {        
        // Connect master database to get language details
        Helpers::switchDatabaseConnection('mysql', $request);
        $languages = DB::table('language')->get();    
        
        // Connect tenant database
        Helpers::switchDatabaseConnection('tenant', $request);

        $missionData = Mission::find($id);
        if (!$missionData) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20032'),
                                        trans('api_error_messages.custom_error_message.20032'));
        } 
        // Server side validataions for mission
        $validator = Validator::make($request->toArray(), [
                            "theme_id" => "required", 
                            "mission_detail" => "required", 
                            "mission_type" => [Rule::in(config('constants.mission_type'))], 
                            "organisation" => "required", 
                            "publication_status" => ['required', Rule::in(config('constants.publication_status'))],
                            "location" => "required",
                            "goal_objective" => "required_if:mission_type,GOAL"]);
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20106'),
                                        $validator->errors()->first());
        }   
        // Server side validataions for location
        $validator = Validator::make($request->location, [
                            "city_id" => "required", 
                            "country_code" => "required"]);
        if ($validator->fails()) {
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('api_error_messages.custom_error_code.ERROR_20106'),
                                        $validator->errors()->first());
        }   

        // Server side validataions for mission language
        foreach ($request->mission_detail as $value) {                    
            $languageValidator = Validator::make($value, ["lang" => "required", 
            											"title" => "required"]);
            if ($languageValidator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $languageValidator->errors()->first());
            } 
        }

        // Server side validataions for media images
        foreach ($request->media_images as $value) {                    
            $validator = Validator::make($value, ["media_name" => "required", 
                                                  "media_type" => [Rule::in(config('constants.image_types'))], 
                                                  "media_path" => "required_without:media_id"]);
            if ($validator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $validator->errors()->first());
            }  
        }

        // Server side validataions for media videos
        foreach ($request->media_videos as $value) { 
            $validator = Validator::make($value, ["media_name" => "required", 
                                                  "media_type" => "required", 
                                                  "media_path" => "required_without:media_id" ]);
            if ($validator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $validator->errors()->first());
            }
        }
                   
        // Server side validataions for documents
        foreach ($request->documents as $value) { 
            $validator = Validator::make($value, ["document_name" => "required", 
                                                  "document_type" => [Rule::in(config('constants.document_types'))],
                                                  "document_path" => "required_without:document_id" ]);
           if ($validator->fails()) {
                return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_422'),
                                            trans('api_error_messages.status_type.HTTP_STATUS_TYPE_422'),
                                            trans('api_error_messages.custom_error_code.ERROR_20106'),
                                            $validator->errors()->first());
            }   
        }

        try { 
            // Set data for update record
            $start_date = $end_date = NULL;
            if (isset($request->start_date))
                $start_date = ($request->start_date != '') ? Carbon::parse($request->start_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
            if (isset($request->end_date))
                $end_date = ($request->end_date != '') ? Carbon::parse($request->end_date)->format(config('constants.DB_DATE_FORMAT')) : NULL;
            $application_deadline = (isset($request->application_deadline) && ($request->application_deadline != '')) ? Carbon::parse($request->application_deadline)->format(config('constants.DB_DATE_FORMAT')) : NULL;

            $country_id = Helpers::getCountryId($request->location['country_code']);
            $missionData = array('theme_id' => $request->theme_id, 
                                 'city_id' => $request->location['city_id'], 
                                 'country_id' => $country_id, 
                                 'start_date' => $start_date, 
                                 'end_date' => $end_date, 
                                 'total_seats' => (isset($request->total_seats) && ($request->total_seats != '')) ? $request->total_seats : NULL,
                                 'application_deadline' => $application_deadline, 
                                 'publication_status' => $request->publication_status, 
                                 'organisation_id' => $request->organisation['organisation_id'], 
                                 'organisation_name' => $request->organisation['organisation_name'],
                                 'mission_type' => $request->mission_type,
                                 'goal_objective' => $request->goal_objective);
            
            // Update record 
            Mission::where('mission_id', $id)->update($missionData);
           
            // Add/Update mission title 
            foreach ($request->mission_detail as $value) {    
                $language = $languages->where('code', $value['lang'])->first();
                $missionLanguage = array('mission_id' => $id, 
                                        'language_id' => $language->language_id, 
                                        'title' => $value['title'], 
                                        'short_description' => (isset($value['short_description'])) ? $value['short_description'] : NULL,                                        'description' => serialize($value['section']),
                                        'objective' => $value['objective']);

                $languageData = MissionLanguage::where('mission_id', $id)
                                        ->where('language_id', $language->language_id)
                                        ->count();
                if (!empty($languageData))
                    MissionLanguage::where('mission_id', $id)
                                        ->where('language_id', $language->language_id)
                                        ->update($missionLanguage);
                else
                    MissionLanguage::create($missionLanguage);
                    
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
                    MissionMedia::where('mission_id', $id)->update($media);
                }
                
                $missionMedia = array('mission_id' => $id, 
                                      'media_name' => $value['media_name'], 
                                      'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION), 
                                      'media_path' => $filePath,
                                      'default' => $default);
                
                $mediaData = MissionMedia::where('mission_id', $id)
                                        ->where('mission_media_id', $value['media_id'])
                                        ->count();
                if (!empty($mediaData))
                    MissionMedia::where('mission_id', $id)
                                        ->where('mission_media_id', $value['media_id'])
                                        ->update($missionMedia);
                else
                    MissionMedia::create($missionMedia);

                unset($missionMedia);
            }

            $defaultData = MissionMedia::where('mission_id', $id)
            							->where('default', '1')->count();

            if (($isDefault == 0) && ($defaultData == 0)) {
            	$mediaData = MissionMedia::where('mission_id', $id)->orderBy('mission_media_id', 'ASC')->first();
            	$missionMedia = array('default' => '1');
                MissionMedia::where('mission_media_id', $mediaData->mission_media_id)->update($missionMedia);
            }

            // Add/Update mission media videos
            foreach ($request->media_videos as $value) { 
                $missionMedia = array('mission_id' => $id, 
                                      'media_name' => $value['media_name'], 
                                      'media_type' => pathinfo($value['media_name'], PATHINFO_EXTENSION),
                                      'media_path' => $value['media_path']);
                $mediaData = MissionMedia::where('mission_id', $id)
                                        ->where('mission_media_id', $value['media_id'])
                                        ->count();
                if (!empty($mediaData))
                    MissionMedia::where('mission_id', $id)
                                        ->where('mission_media_id', $value['media_id'])
                                        ->update($missionMedia);
                else
                    MissionMedia::create($missionMedia);

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

                $documentData = MissionDocument::where('mission_id', $id)
                                        ->where('mission_document_id', $value['document_id'])
                                        ->count();
                if (!empty($documentData))
                    MissionDocument::where('mission_id', $id)
                                        ->where('mission_document_id', $value['document_id'])
                                        ->update($missionDocument);
                else
                    MissionDocument::create($missionDocument);
                unset($missionDocument);
            }
           
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_MISSION_UPDATE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);     

        } catch (\Exception $e) { 
            // Any other error occured when trying to update data into database.
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_400'), 
                                    trans('api_error_messages.status_type.HTTP_STATUS_TYPE_400'), 
                                    trans('api_error_messages.custom_error_code.ERROR_20104'), 
                                    trans('api_error_messages.custom_error_message.20104'));
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {  
            $mission = Mission::findOrFail($id);
            $mission->delete();
            $mission->missionMedia()->delete();
            $mission->missionLanguage()->delete();
            $mission->missionDocument()->delete();

            $apiStatus = trans('api_success_messages.status_code.HTTP_STATUS_204');
            $apiMessage = trans('api_success_messages.success_message.MESSAGE_MISSION_DELETE_SUCCESS');
            return Helpers::response($apiStatus, $apiMessage);            
        } catch(\Exception $e){
            return Helpers::errorResponse(trans('api_error_messages.status_code.HTTP_STATUS_403'), 
                                        trans('api_error_messages.status_type.HTTP_STATUS_TYPE_403'), 
                                        trans('api_error_messages.custom_error_code.ERROR_20108'), 
                                        trans('api_error_messages.custom_error_message.20108'));
        }
    }
}
