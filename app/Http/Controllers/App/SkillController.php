<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\Skill\SkillRepository;
use Illuminate\Http\{Request, Response, JsonResponse};
use PDOException;
use App\Helpers\ResponseHelper;

class SkillController extends Controller
{
    /**
     * @var App\Repositories\Skill\SkillRepository 
     */
    private $skill;
    
    /**
     * @var Illuminate\Http\Response
     */
    private $response;
    
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Skill\SkillRepository $skill
     * @param Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(SkillRepository $skill, Response $response)
    {
         $this->skill = $skill;
         $this->response = $response;
    }
    
    /**
     * Display listing of footer pages
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request): JsonResponse
    {       
        try {
            $skillArray = [];
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 
                        env('TENANT_DEFAULT_LANGUAGE_CODE'); 

            $skill = $this->skill->skillList($request);
            $skillData = $skill->toArray();
            if ($skillData) {
                foreach ($skillData as $key => $value) {    
                    $key = array_search($local, array_column($value['translations'], 'lang')); 
                    $skillArray[$value["skill_id"]] = $value["translations"][$key]["title"];    
                }
            } 
            // Set response data
            $apiStatus = $this->response->status();
            $apiMessage = (empty($skillArray)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') : trans('messages.success.MESSAGE_SKILL_LISTING');
            return ResponseHelper::success($apiStatus, $apiMessage, $skillArray);                  
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
