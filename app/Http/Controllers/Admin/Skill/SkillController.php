<?php
namespace App\Http\Controllers\Admin\Skill;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Skill\SkillRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use PDOException;
use Validator;
use DB;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    use RestExceptionHandlerTrait;
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
     * @param App\Repositories\User\SkillRepository $skillRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(SkillRepository $skillRepository, ResponseHelper $responseHelper)
    {
        $this->skillRepository = $skillRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $skills = $this->skillRepository->skillDetails($request);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($skills->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
             : trans('messages.success.MESSAGE_SKILL_LISTING');
            return $this->responseHelper->successWithPagination(Response::HTTP_OK, $apiMessage, $skills);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                [
                    "skill_name" => "required|unique:skill,skill_name,NULL,skill_id,deleted_at,NULL",
                    "translations" => "required"
                ]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
            
            // Create new skill
            $skill = $this->skillRepository->store($request->all());

            // Set response data
            $apiData = ['skill_id' => $skill->skill_id];
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_SKILL_CREATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_PARENT_SKILL_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_PARENT_SKILL_NOT_FOUND')
            );
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->all(),
                ["skill_name" => [
                    "sometimes",
                    "required",
                    Rule::unique('skill')->ignore($id, 'skill_id,deleted_at,NULL')],
                "translations" => "sometimes|required"]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
         
            // Update skill
            try {
                $skill = $this->skillRepository->update($request->toArray(), $id);
            } catch (ModelNotFoundException $e) {
                return $this->modelNotFound(
                    config('constants.error_codes.ERROR_SKILL_NOT_FOUND'),
                    trans('messages.custom_error_message.ERROR_SKILL_NOT_FOUND')
                );
            }

            // Set response data
            $apiData = ['skill_id' => $skill->skill_id];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_SKILL_UPDATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_SKILL_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_SKILL_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Display the specified skill detail.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            try {
                $skillDetail = $this->skillRepository->find($id);
            } catch (ModelNotFoundException $e) {
                return $this->modelNotFound(
                    config('constants.error_codes.ERROR_SKILL_NOT_FOUND'),
                    trans('messages.custom_error_message.ERROR_SKILL_NOT_FOUND')
                );
            }

            $apiData = $skillDetail->toArray();
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_SKILL_FOUND');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_SKILL_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_SKILL_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $skill = $this->skillRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_SKILL_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_SKILL_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_SKILL_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
