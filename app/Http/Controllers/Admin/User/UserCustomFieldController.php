<?php
namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\UserCustomField\UserCustomFieldRepository;
use App\Models\UserCustomField;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use Validator;
use PDOException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;

class UserCustomFieldController extends Controller
{
    use RestExceptionHandlerTrait;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $customFields = $this->userCustomFieldRepository->userCustomFieldList($request);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($customFields->isEmpty()) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
             : trans('messages.success.MESSAGE_CUSTOM_FIELD_LISTING');
            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $customFields);
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
     * Store user custom field
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
                "is_mandatory" => "required|boolean",
                "translations" => "required",
				"translations.*.lang" => "max:2",
                "translations.*.values" => Rule::requiredIf(
                    $request->type == config('constants.custom_field_types.DROP-DOWN') ||
                    $request->type == config('constants.custom_field_types.RADIO')
                ),
                ]
            );
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
            
            // Create new user custom field record
            $customField = $this->userCustomFieldRepository->store($request->toArray());
            
            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_FIELD_ADDED');
            $apiData = ['field_id' => $customField['field_id']];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_INVALID_DATA'),
                trans('messages.custom_error_message.ERROR_USER_CUSTOM_FIELD_INVALID_DATA')
            );
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

    /**
     * Update user custom field
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Server side validataions
            $validator = Validator::make(
                $request->toArray(),
                ["name" => "sometimes|required",
                "is_mandatory" => "sometimes|required|boolean",
                "type" => [
                    "sometimes",
                    "required",
                    Rule::in(config('constants.custom_field_types'))],
				"translations.*.lang" => "max:2",
                "translations.*.values" =>
                Rule::requiredIf($request->type == config('constants.custom_field_types.DROP-DOWN')
                    || $request->type == config('constants.custom_field_types.RADIO')),
                ]
            );
            // If post parameter have any missing parameter
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }
            
            $customField = $this->userCustomFieldRepository->update($request->toArray(), $id);
            
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_FIELD_UPDATED');
            $apiData = ['field_id' => $customField['field_id']];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_INVALID_DATA'),
                trans('messages.custom_error_message.ERROR_USER_CUSTOM_FIELD_INVALID_DATA')
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_CUSTOM_FIELD_NOT_FOUND')
            );
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $customField = $this->userCustomFieldRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_FIELD_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_CUSTOM_FIELD_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_CUSTOM_FIELD_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
