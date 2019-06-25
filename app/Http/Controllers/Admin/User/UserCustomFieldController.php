<?php
namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserCustomField\UserCustomFieldRepository;
use Illuminate\Support\Facades\Input;
use App\Models\UserCustomField;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use Validator;
use PDOException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserCustomFieldController extends Controller
{
    /**
     * User custom field
     *
     * @var App\Repositories\UserCustomField\UserCustomFieldRepository
     */
    private $userCustomFieldRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\UserCustomField\UserCustomFieldRepository $userCustomFieldRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(UserCustomFieldRepository $userCustomFieldRepository, ResponseHelper $responseHelper)
    {
        $this->userCustomFieldRepository = $userCustomFieldRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $customFields = $this->userCustomFieldRepository->UserCustomFieldList($request);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($customFields->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
             : trans('messages.success.MESSAGE_CUSTOM_FIELD_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $customFields);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Store user custom field
     *
     * @param \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->toArray(),
                ["name" => "required",
                "type" => ['required',
                    Rule::in(config('constants.custom_field_types'))],
                "is_mandatory" => "required",
                "translations" => "required",
                "translations.*.values" => Rule::requiredIf(
                    $request->type == config('constants.custom_field_types.DROP-DOWN') ||
                    $request->type == config('constants.custom_field_types.RADIO')
                ),
                ]
            );
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_100003'),
                    $validator->errors()->first()
                );
            }
            
            // Create new user custom field record
            $customField = $this->userCustomFieldRepository->store($request);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_FIELD_ADDED');
            $apiData = ['field_id' => $customField['field_id']];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update user custom field
     *
     * @param \Illuminate\Http\Request  $request
     * @param int  $id
     * @return mixed
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->toArray(),
                ["type" => [Rule::in(config('constants.custom_field_types'))],
                "translations.*.values" =>
                Rule::requiredIf($request->type == config('constants.custom_field_types.DROP-DOWN')
                    || $request->type == config('constants.custom_field_types.RADIO')),
                ]
            );
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    trans('messages.status_code.HTTP_STATUS_UNPROCESSABLE_ENTITY'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_100003'),
                    $validator->errors()->first()
                );
            }
            
            $customField = $this->userCustomFieldRepository->update($request, $id);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_FIELD_UPDATED');
            $apiData = ['field_id' => $customField['field_id']];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100004'));
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $customField = $this->userCustomFieldRepository->delete($id);
            
            // Set response data
            $apiStatus = trans('messages.status_code.HTTP_STATUS_NO_CONTENT');
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_FIELD_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100004'));
        }
    }
}
