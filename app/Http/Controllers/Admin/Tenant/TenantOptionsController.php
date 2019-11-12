<?php
namespace App\Http\Controllers\Admin\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\TenantOption\TenantOptionRepository;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;
use Validator;
use App\Jobs\DownloadAssestFromLocalDefaultThemeToLocalStorageJob;
use App\Traits\RestExceptionHandlerTrait;
use App\Exceptions\BucketNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUploadException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\TenantDomainNotFoundException;
use App\Jobs\ResetStyleSettingsJob;
use App\Jobs\UpdateStyleSettingsJob;
use App\Services\CustomStyling\CustomStylingService;
use App\Jobs\CopyDefaultThemeImagesToTenantImagesJob;
use App\Events\User\UserActivityLogEvent;

//!  Tenant options controller
/*!
This controller is responsible for handling tenant options store, update, download assets,
reset style, update style and update image operations.
 */
class TenantOptionsController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\TenantOption\TenantOptionRepository
     */
    private $tenantOptionRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * @var App\Services\CustomStyling\CustomStylingService
     */
    private $customStylingService;

    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @param  App\Services\CustomStyling\CustomStylingService $customStylingService
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(
        TenantOptionRepository $tenantOptionRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        CustomStylingService $customStylingService,
        Request $request
    ) {
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
        $this->customStylingService = $customStylingService;
        $this->userApiKey = $request->header('php-auth-user');
    }

    /**
     * Reset to default style
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetStyleSettings(Request $request): JsonResponse
    {
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
    
        // Database connection with master database
        $this->helpers->switchDatabaseConnection('mysql', $request);
        
        // Dispatch job, that will store in master database
        dispatch(new ResetStyleSettingsJob($tenantName));

        // Database connection with tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);
        
        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_CUSTOM_STYLE_RESET_SUCCESS');
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }

    /**
     * Update tenant custom styling data: primary color, secondary color and custom css
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStyleSettings(Request $request): JsonResponse
    {
        $isVariableScss = 0;
        $fileName = '';
        
        if (!$request->hasFile('custom_scss_file') && empty($request->primary_color)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_REQUIRED_FIELDS_FOR_UPDATE_STYLING'),
                trans('messages.custom_error_message.ERROR_REQUIRED_FIELDS_FOR_UPDATE_STYLING')
            );
        }

        $updatedTenantOptionIds = $this->tenantOptionRepository->updateStyleSettings($request);

        if (!empty($updatedTenantOptionIds)) {
            $requestArray = $request->toArray();
            foreach ($updatedTenantOptionIds as $tenantOptionId) {
                $requestArray['custom_scss_file'] = '';
                // Make activity log for tenant option primary key or secondary key
                event(new UserActivityLogEvent(
                    config('constants.activity_log_types.STYLE'),
                    config('constants.activity_log_actions.UPDATED'),
                    config('constants.activity_log_user_types.API'),
                    $this->userApiKey,
                    get_class($this),
                    $requestArray,
                    null,
                    $tenantOptionId
                ));
            }
        }
        
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        
        if ($request->hasFile('custom_scss_file')) {
            $response = $this->customStylingService->uploadImage($request);
            if (!is_null($response)) {
                return $response;
            }
        }
        $options['isVariableScss'] = $isVariableScss;
        
        if (isset($request->primary_color) && $request->primary_color!='') {
            $options['primary_color'] = $request->primary_color;
        }

        // Database connection with master database
        $this->helpers->switchDatabaseConnection('mysql', $request);
        
        // Create new job that will take tenantName, options, and uploaded file path as an argument.
        // Dispatch job, that will store in master database

        dispatch(new UpdateStyleSettingsJob($tenantName, $options, $fileName));
        $this->helpers->switchDatabaseConnection('tenant', $request);

        if (!empty($fileName)) {
            $requestArray = $request->toArray();
            $requestArray['custom_scss_file'] = $filePath;

            // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.STYLE'),
                config('constants.activity_log_actions.UPDATED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                $requestArray,
                null,
                null
            ));
        }
        

        // Database connection with tenant database
        $this->helpers->switchDatabaseConnection('tenant', $request);
        
        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_CUSTOM_STYLE_UPLOADED_SUCCESS');

        return $this->responseHelper->success($apiStatus, $apiMessage);
    }

    /**
     * It will give list of all assets files from s3 to download it.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadStyleFiles(Request $request): JsonResponse
    {
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        try {
            $assetFilesArray = $this->s3helper->getAllScssFiles($tenantName);
        } catch (BucketNotFoundException $e) {
            throw $e;
        }
        
        if (count($assetFilesArray) > 0) {
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_ASSETS_FILES_LISTING');
            return $this->responseHelper->success($apiStatus, $apiMessage, $assetFilesArray);
        } else {
            return $this->responseHelper->error(
                Response::HTTP_NOT_FOUND,
                Response::$statusTexts[Response::HTTP_NOT_FOUND],
                config('constants.error_codes.ERROR_NO_FILES_FOUND_IN_ASSETS_FOLDER'),
                trans('messages.custom_error_message.ERROR_NO_FILES_FOUND_IN_ASSETS_FOLDER')
            );
        }
    }

    /**
     * It will update image on S3
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateImage(Request $request): JsonResponse
    {
        $validFileTypesArray = ['image/jpeg','image/svg+xml','image/png'];
        // Server side validataions
        $validator = Validator::make(
            $request->toArray(),
            [
                "image_name" => "required"
            ]
        );

        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_IMAGE_UPLOAD_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        $file = $request->file('image_file');
        $fileName = $request->image_name;
        $fileNameExtension = substr(strrchr($fileName, '.'), 1);
        
        $validateResponse = $this->customStylingService->checkFileValidations($request);
        if (!is_null($validateResponse)) {
            return $validateResponse;
        }
       
        // If request parameter have any error
        if (!in_array($file->getMimeType(), $validFileTypesArray) &&
        $fileNameExtension === $file->getClientOriginalExtension()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_NOT_VALID_EXTENSION'),
                trans('messages.custom_error_message.ERROR_NOT_VALID_IMAGE_FILE_EXTENSION')
            );
        }

        try {
            // Get domain name from request and use as tenant name.
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
        } catch (TenantDomainNotFoundException $e) {
            throw $e;
        }

        // request array to make activity log
        $requestArray = $request->toArray();

        if (Storage::disk('s3')->exists($tenantName)) {
            $response = $this->customStylingService->uploadFileOnS3($request);
            if (!is_null($response)) {
                return $response;
            }
            // Upload file on s3
            Storage::disk('s3')->put(
                '/'.$tenantName.'/assets/images/'.$fileName,
                file_get_contents(
                    $file->getRealPath()
                ),
                [
                    'mimetype' => $file->getMimeType()
                ]
            );
            $requestArray['image_file'] = 'https://'.env('AWS_S3_BUCKET_NAME').'.s3.'
                .env("AWS_REGION").'.amazonaws.com/'.$tenantName.'/assets/images/'.$fileName;
        } else {
            throw new BucketNotFoundException(
                trans('messages.custom_error_message.ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3'),
                config('constants.error_codes.ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3')
            );
        }

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.STYLE_IMAGE'),
            config('constants.activity_log_actions.UPDATED'),
            config('constants.activity_log_user_types.API'),
            $this->userApiKey,
            get_class($this),
            $requestArray,
            null,
            null
        ));
                  
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_IMAGE_UPLOADED_SUCCESSFULLY');

        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
    
    /**
     * Store tenant option values
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function storeTenantOption(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "option_name" => "required|unique:tenant_option,option_name,NULL,tenant_option_id,deleted_at,NULL",
                "option_value" => "required",
                "option_value.translations.*.lang" => "max:2"
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_OPTION_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        
        $data = $request->toArray();
        $data['option_value'] =
        (gettype($request->option_value)=="array") ? serialize($request->option_value) :
        $request->option_value;

        $tenantOption = $this->tenantOptionRepository->store($data);
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_TENANT_OPTION_CREATED');
        
        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.TENANT_OPTION'),
            config('constants.activity_log_actions.CREATED'),
            config('constants.activity_log_user_types.API'),
            $this->userApiKey,
            get_class($this),
            $request->toArray(),
            null,
            $tenantOption->tenant_option_id
        ));

        return $this->responseHelper->success($apiStatus, $apiMessage);
    }

    /**
     * Update tenant option value
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function updateTenantOption(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                "option_name" => "required",
                "option_value" => "required",
                "option_value.translations.*.lang" => "max:2"
            ]
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_OPTION_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        try {
            $data['option_name'] = $request->option_name;
            
            $tenantOption = $this->tenantOptionRepository->getOptionWithCondition($data);

            $updateData['option_value'] = (gettype($request->option_value)=="array")
            ? serialize($request->option_value) : $request->option_value;
            $tenantOption->update($updateData);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_OPTION_UPDATED');
            
             // Make activity log
            event(new UserActivityLogEvent(
                config('constants.activity_log_types.TENANT_OPTION'),
                config('constants.activity_log_actions.UPDATED'),
                config('constants.activity_log_user_types.API'),
                $this->userApiKey,
                get_class($this),
                $request->toArray(),
                null,
                $tenantOption->tenant_option_id
            ));

            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_OPTION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_OPTION_NOT_FOUND')
            );
        }
    }

    /**
     * Reset to default asset images
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetAssetsImages(Request $request): JsonResponse
    {
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        
        // Copy default theme folder to tenant folder on s3
        dispatch(new CopyDefaultThemeImagesToTenantImagesJob($tenantName));

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_ASSET_IMAGES_RESET_SUCCESS');
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
