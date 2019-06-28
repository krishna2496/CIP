<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\MissionTheme\MissionThemeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use PDOException;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;

class ThemeController extends Controller
{
    /**
     * @var App\Repositories\Theme\MissionThemeRepository
     */
    private $missionThemeRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
        
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Theme\MissionThemeRepository $missionThemeRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(MissionThemeRepository $missionThemeRepository, ResponseHelper $responseHelper)
    {
        $this->missionThemeRepository = $missionThemeRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $themeArray = [];
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') :
                        env('TENANT_DEFAULT_LANGUAGE_CODE');

            $theme = $this->missionThemeRepository->missionThemeList($request);
            $themeData = $theme->toArray();
            if ($themeData) {
                foreach ($themeData as $key => $value) {
                    $key = array_search($local, array_column($value['translations'], 'lang'));
                    $themeArray[$value["mission_theme_id"]] = $value["translations"][$key]["title"];
                }
            }

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (empty($themeArray)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_THEME_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $themeArray);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURED'));
        }
    }
}
