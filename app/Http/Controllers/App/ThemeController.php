<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\MissionTheme\MissionThemeRepository;
use Illuminate\Http\{Request, Response, JsonResponse};
use Illuminate\Support\Facades\Input;
use Validator, DB, PDOException;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ThemeController extends Controller
{
    /**
     * @var App\Repositories\Theme\ThemeRepository 
     */
    private $theme;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
    
    
    /**
     * Create a new controller instance.
     *
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
     * @return mixed
     */
    public function index(Request $request)
    {
       
        try { 
            $themeArray = [];
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 
                        env('TENANT_DEFAULT_LANGUAGE_CODE'); 

            $theme = $this->missionTheme->missionThemeList($request);
            if ($theme) {
                foreach ($theme as $key => $value) {    
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
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
