<?php

namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\{Request, Response};
use App\Repositories\Mission\MissionRepository;
use Illuminate\Support\Facades\{DB, Config};
use App\Models\{Mission, MissionLanguage, MissionDocument, MissionMedia, MissionTheme, MissionApplication};
use App\Helpers\{Helpers, LanguageHelper, ResponseHelper};
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MissionController extends Controller
{
    /**
     * @var App\Models\Mission
     */
    private $mission;
    
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
     * Get missions listing
     *  
     * @param Request $request
     * @return mixed
     */
    public function missionList(Request $request) 
    {   
        try { 
            $missions = $this->mission->missionDetail($request);
            
            $apiData = $missions;
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LIST_SUCCESS');
            return ResponseHelper::successWithPagination($apiStatus, $apiMessage,$apiData);

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get missions listing
     *  
     * @param Request $request
     * @return mixed
     */
    public function appMissionList(Request $request) 
    {   
        try { 
            $missions = $this->mission->appMissions($request);
            
            $apiData = $missions;
            $apiStatus = app('Illuminate\Http\Response')->status();
            $apiMessage = trans('messages.success.MESSAGE_MISSION_LIST_SUCCESS');
            return ResponseHelper::successWithPagination($apiStatus, $apiMessage,$apiData);

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
