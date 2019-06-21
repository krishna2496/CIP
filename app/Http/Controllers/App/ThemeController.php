<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\MissionTheme\MissionThemeRepository;
use Illuminate\Http\{Request, Response, JsonResponse};
use Illuminate\Support\Facades\Input;
use PDOException;
use App\Helpers\ResponseHelper;

class ThemeController extends Controller
{
    /**
     * @var App\Repositories\Theme\ThemeRepository 
     */
    private $missionTheme;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
        
    /**
     * Create a new controller instance.
     * 
     * @param App\Repositories\Theme\ThemeRepository $missionTheme
     * @param Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(MissionThemeRepository $missionTheme, Response $response)
    {
         $this->missionTheme = $missionTheme;
         $this->response = $response;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
       
        try { 
            $themeArray = [];
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 
                        env('TENANT_DEFAULT_LANGUAGE_CODE'); 

            $theme = $this->missionTheme->missionThemeList($request);
            $themeData = $theme->toArray();
            if ($themeData) {
                foreach ($themeData as $key => $value) {    
                    $key = array_search($local, array_column($value['translations'], 'lang')); 
                    $themeArray[$value["mission_theme_id"]] = $value["translations"][$key]["title"];    
                }
            } 

            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = (empty($themeArray)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') : trans('messages.success.MESSAGE_THEME_LISTING');
            return ResponseHelper::success($apiStatus, $apiMessage, $themeArray);                  
        }
         catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
