<?php
namespace App\Http\Controllers\Admin\Mission;

use App\Http\Controllers\Controller;
use Illuminate\Http\{Request, Response, JsonResponse};
use Illuminate\Support\Facades\{Input, Config};
use Illuminate\Validation\Rule;
use App\Repositories\Mission\MissionRepository;
use App\Models\{Mission, MissionLanguage, MissionDocument, MissionMedia, MissionTheme, MissionApplication};
use App\Helpers\{Helpers, ResponseHelper, LanguageHelper};
use Validator, DB;

class MissionController extends Controller
{   
    /**
     * @var App\Models\Mission
     */
    private $user;
    
    /**
     * Create a new Mission controller instance.
     *
     * @param  App\Repositories\Mission\MissionRepository $mission
     * @return void
     */
    public function __construct(MissionRepository $mission, Response $response)
    {
        $this->mission = $mission;
        $this->response = $response;
    }

    /**
     * Display a listing of Mission.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request): JsonResponse
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
                $apiMessage = trans('messages.success.MESSAGE_NO_DATA_FOUND');
                return ResponseHelper::success($apiStatus, $apiMessage);
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
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LIST_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);                  
        } catch(\Exception $e) {
            // Catch database exception
            return ResponseHelper::error(
                trans('messages.status_code.HTTP_STATUS_500'), 
                trans('messages.status_type.HTTP_STATUS_TYPE_500'), 
                trans('messages.custom_error_code.ERROR_40018'), 
                trans('messages.custom_error_message.40018')
            );           
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make($request->all(), 
            [
                "theme_id" => "required", 
                "mission_type" => ['required', Rule::in(config('constants.mission_type'))],
                "location" => "required", 
                "location.city_id" => "required",
                "location.country_code" => "required",
                "mission_detail" => "required",
                "mission_detail.*.lang" => "required",
                "mission_detail.*.title" => "required", 
                "organisation" => "required", 
                "publication_status" => ['required', Rule::in(config('constants.publication_status'))],
                "goal_objective" => "required_if:mission_type,GOAL",
                "media_images.*.media_name" => "required", 
                "media_images.*.media_type" => ['required', Rule::in(config('constants.image_types'))], 
                "media_images.*.media_path" => "required", 
                "media_videos.*.media_name" => "required", 
                "media_videos.*.media_path" => "required", 
                "documents.*.document_name" => "required", 
                "documents.*.document_type" => ['required', Rule::in(config('constants.document_types'))],
                "documents.*.document_path" => "required",
                "start_date" => "before:end_date",
                "end_date" => "after:start_date",
                "total_seats" => "numeric"
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return ResponseHelper::error(
                trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_300000'),
                $validator->errors()->first()
            );
        }

        try { 

            $mission = $this->mission->store($request);
                       
            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_STATUS_CREATED');
            $apiMessage = trans('messages.success.MESSAGE_MISSION_ADD_SUCCESS');
            $apiData = ['mission_id' => $mission->mission_id];
            return ResponseHelper::success($apiStatus, $apiMessage, $apiData);

        } catch(PDOException $e) {            
            throw new PDOException($e->getMessage());            
        } catch(\Exception $e) {            
            throw new \Exception($e->getMessage());            
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
    public function update(Request $request, $id): JsonResponse
    {   
        // Server side validataions
        $validator = Validator::make($request->all(), 
            [
                "mission_type" => [Rule::in(config('constants.mission_type'))],
                "location.city_id" => "required_with:location",
                "location.country_code" => "required_with:location",
                "mission_detail" => "required",
                "mission_detail.*.lang" => "required_with:mission_detail",
                "mission_detail.*.title" => "required_with:mission_detail",
                "publication_status" => [Rule::in(config('constants.publication_status'))],
                "goal_objective" => "required_if:mission_type,GOAL",
                "media_images.*.media_name" => "required_with:media_images", 
                "media_images.*.media_type" => ['required_with:media_images', Rule::in(config('constants.image_types'))], 
                "media_images.*.media_path" => "required_with:media_images", 
                "media_videos.*.media_name" => "required_with:media_videos", 
                "media_videos.*.media_path" => "required_with:media_videos", 
                "documents.*.document_name" => "required_with:documents", 
                "documents.*.document_type" => ['required_with:documents', Rule::in(config('constants.document_types'))],
                "documents.*.document_path" => "required_with:documents",
                "start_date" => "before:end_date",
                "end_date" => "after:start_date",
                "total_seats" => "numeric"
            ]
        );
        
        // If request parameter have any error
        if ($validator->fails()) {
            return ResponseHelper::error(
                trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_300000'),
                $validator->errors()->first()
            );
        }

        try { 
            
            $this->mission->update($request, $id);
            // Set response data
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_MISSION_UPDATE_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage);     

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.400003'));
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy($id): JsonResponse
    {
        try {              
            $mission = $this->mission->delete($id); 

            $apiStatus = trans('messages.status_code.HTTP_STATUS_NO_CONTENT');
            $apiMessage = trans('messages.success.MESSAGE_MISSION_DELETE_SUCCESS');
            return ResponseHelper::success($apiStatus, $apiMessage);            
        } catch(\Exception $e){ 
            return ResponseHelper::error(
                trans('messages.status_code.HTTP_STATUS_FORBIDDEN'), 
                trans('messages.status_type.HTTP_STATUS_TYPE_403'), 
                trans('messages.custom_error_code.ERROR_400004'), 
                trans('messages.custom_error_message.400004')
            );
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @return \Illuminate\Http\Response
     */
    public function missionApplications(Request $request, int $missionId): JsonResponse
    {
        try {            
            $applicationList = $this->mission->missionApplications($request, $missionId);
            $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATION_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return ResponseHelper::successWithPagination($this->response->status(), $responseMessage, $applicationList);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Display specified resource.
     *
     * @param int $missionId
     * @param int $applicationId
     * @return \Illuminate\Http\Response
     */
    public function missionApplication(int $missionId, int $applicationId): JsonResponse
    {
        try {            
            $applicationList = $this->mission->missionApplication($missionId, $applicationId);
            $responseMessage = (count($applicationList) > 0) ? trans('messages.success.MESSAGE_APPLICATION_LISTING') : trans('messages.success.MESSAGE_NO_RECORD_FOUND');
            
            return ResponseHelper::success($this->response->status(), $responseMessage, $applicationList);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Update resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $missionId
     * @param int $applicationId
     * @return \Illuminate\Http\Response
     */
    public function updateApplication(Request $request, int $missionId, int $applicationId): JsonResponse
    {
         try {
            $validator = Validator::make($request->toArray(), [
                'approval_status' => Rule::in(config('constants.application_status')),
            ]);

            // If request parameter have any error
            if ($validator->fails()) {
                return ResponseHelper::error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_400000'),
                    $validator->errors()->first()
                );
            }

            $application = $this->mission->updateApplication($request, $missionId, $applicationId);

            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = trans('messages.success.MESSAGE_APPLICATION_UPDATED');
            
            return ResponseHelper::success($apiStatus, $apiMessage);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
