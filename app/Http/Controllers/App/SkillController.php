<?php
namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Repositories\Skill\SkillRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use PDOException;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;

class SkillController extends Controller
{
    /**
     * @var App\Repositories\Skill\SkillRepository
     */
    private $skillRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
        
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\Skill\SkillRepository $skill
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(SkillRepository $skillRepository, ResponseHelper $responseHelper)
    {
        $this->skillRepository = $skillRepository;
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
            $skillArray = [];
            $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') :
                        env('TENANT_DEFAULT_LANGUAGE_CODE');

            $skill = $this->skillRepository->skillList($request);
            $skillData = $skill->toArray();
            if ($skillData) {
                foreach ($skillData as $key => $value) {
                    $key = array_search($local, array_column($value['translations'], 'lang'));
                    $skillArray[$value["skill_id"]] = $value["translations"][$key]["title"];
                }
            }
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (empty($skillArray)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND') :
             trans('messages.success.MESSAGE_SKILL_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $skillArray);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
