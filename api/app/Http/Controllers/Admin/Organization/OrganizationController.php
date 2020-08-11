<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;
use App\Repositories\Organization\OrganizationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use InvalidArgumentException;
use App\Events\User\UserActivityLogEvent;

//!  Organization controller
/*!
This controller is responsible for handling organization listing, show, store, update and delete operations.
 */
class OrganizationController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    /**
     * @var App\Repositories\Organization\OrganizationRepository
     */
    private $organizationRepository;
    /**
     * @var string
     */
    private $userApiKey;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        OrganizationRepository $organizationRepository,
        Request $request
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->responseHelper = $responseHelper;
        $this->userApiKey =$request->header('php-auth-user');
    }

    /**
     * Fetch all organizations
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $organizations = $this->organizationRepository->getOrganizationList($request);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($organizations->isEmpty()) ? trans('messages.custom_error_message.ERROR_ORGANIZATION_NOT_FOUND')
            : trans('messages.success.MESSAGE_ORGANIZATION_LISTING');

            return $this->responseHelper->successWithPagination($apiStatus, $apiMessage, $organizations);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        }
    }

    /**
     * View organization
     *
     * @param Illuminate\Http\Request $request
     * @param string $organizationId
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $organizationId): JsonResponse
    {
        try {
            // Get organization details
            $organization = $this->organizationRepository->getOrganizationDetails($organizationId);
        
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_ORGANIZATION_FOUND');

            return $this->responseHelper->success($apiStatus, $apiMessage, $organization->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_ORGANIZATION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_ORGANIZATION_NOT_FOUND')
            );
        }
    }

    /**
     * Store organization
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validations
        $validator = Validator::make(
            $request->all(),
            [
                "name" => "required|max:255",
                "legal_number" => "max:255",
                "phone_number" => "max:120",
                "address_line_1" => "max:255",
                "address_line_2" => "max:255",
                "city_id" => "numeric|exists:city,city_id,deleted_at,NULL",
                "country_id" => "numeric|exists:country,country_id,deleted_at,NULL",
                "postal_code" => "max:120",
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_ORGANIZATION_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }

        // update city,state & country id to null if it's blank
        if ($request->has('city_id') && $request->get('city_id')=='') {
            $request->merge(['city_id' => null]);
        }
        if ($request->has('country_id') && $request->get('country_id')=='') {
            $request->merge(['country_id' => null]);
        }

        // Create a new record
        $organization = $this->organizationRepository->store($request);

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.ORGANIZATION'),
            config('constants.activity_log_actions.CREATED'),
            config('constants.activity_log_user_types.API'),
            $this->userApiKey,
            get_class($this),
            $request->toArray(),
            null,
            $organization->organization_id
        ));
        
        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_ORGANIZATION_CREATED');
        $apiData = ['organization_id' => $organization->organization_id];
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Update organization
     *
     * @param Illuminate\Http\Request $request
     * @param string $organizationId
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $organizationId): JsonResponse
    {
        try {
            // Server side validations
            $validator = Validator::make(
                $request->all(),
                [
                    "name" => "sometimes|required|max:255",
                    "legal_number" => "max:255",
                    "phone_number" => "max:120",
                    "address_line_1" => "max:255",
                    "address_line_2" => "max:255",
                    "city_id" => "numeric|exists:city,city_id,deleted_at,NULL",
                    "country_id" => "numeric|exists:country,country_id,deleted_at,NULL",
                    "postal_code" => "max:120",
                ]
            );

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_ORGANIZATION_REQUIRED_FIELDS_EMPTY'),
                    $validator->errors()->first()
                );
            }

            // update city,state & country id to null if it's blank
            if ($request->has('city_id') && $request->get('city_id')=='') {
                $request->merge(['city_id' => null]);
            }
            if ($request->has('country_id') && $request->get('country_id')=='') {
                $request->merge(['country_id' => null]);
            }

            // Update organization details
            $organization = $this->organizationRepository->update($request, $organizationId);
            
            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.ORGANIZATION'),
                config('constants.activity_log_actions.UPDATED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                $request->toArray(),
                null,
                $organization->organization_id
            ));
            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_ORGANIZATION_UPDATED');
            $apiData = ['organization_id' => $organization->organization_id];
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_ORGANIZATION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_ORGANIZATION_NOT_FOUND')
            );
        }
    }

    /**
     * Delete organization
     *
     * @param string $organizationId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(string $organizationId): JsonResponse
    {
        try {
            $isOrganizationLinked = $this->organizationRepository->isOrganizationLinkedtoMission($organizationId);
            if($isOrganizationLinked){
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_ORGANIZATION_LINKED_TOMISSION'),
                    trans('messages.custom_error_message.ERROR_ORGANIZATION_LINKED_TOMISSION')
                ); 
            }
            //Delete organization
            $organization = $this->organizationRepository->delete($organizationId);
            
            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.ORGANIZATION'),
                config('constants.activity_log_actions.DELETED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                [],
                null,
                $organizationId
            ));
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_ORGANIZATION_DELETED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_ORGANIZATION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_ORGANIZATION_NOT_FOUND')
            );
        }
    }
}
