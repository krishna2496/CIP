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
use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use App\Traits\RestExceptionHandlerTrait;
use App\Exceptions\BucketNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUploadException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\TenantDomainNotFoundException;
use App\Jobs\ResetStyleSettingsJob;
use App\Jobs\UpdateStyleSettingsJob;

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
     * Create a new controller instance.
     *
     * @param  App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        TenantOptionRepository $tenantOptionRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
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

        $this->tenantOptionRepository->updateStyleSettings($request);

        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        if ($request->hasFile('custom_scss_file')) {
            $file = $request->file('custom_scss_file');

            // Server side validataions
            $validator = Validator::make(
                $request->toArray(),
                [
                    "custom_scss_file_name" => "required"
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

            // If request parameter have any error
            if ($file->getClientOriginalExtension() !== "scss") {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_NOT_VALID_EXTENSION'),
                    trans('messages.custom_error_message.ERROR_NOT_VALID_EXTENSION')
                );
            }
            
            if ($file->isValid()) {
                $fileName = $request->custom_scss_file_name;

                /* Check user uploading custom style variable file,
                then we need to make it as high priority instead of passed colors. */
                if ($fileName === config('constants.AWS_CUSTOM_STYLE_VARIABLE_FILE_NAME')) {
                    $isVariableScss = 1;
                }
                // Need to get list SCSS files from S3 server and match name with passed file name
                $allSCSSFiles = $this->s3helper->getAllScssFiles($tenantName);
                // if it is not exist then need to throw error
                if (array_search($fileName, array_column($allSCSSFiles['scss_files'], 'scss_file_name')) === false) {
                    // Error: Return like uploaded file name doesn't match with structure.
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE'),
                        trans('messages.custom_error_message.ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE')
                    );
                }
                // Need to upload file on S3 and that function will return uploaded file URL.
                $file = $request->file('custom_scss_file');
                
                $filePath = $tenantName.'/'.config('constants.AWS_S3_ASSETS_FOLDER_NAME').'/'.
                config('constants.AWS_S3_SCSS_FOLDER_NAME').'/'. $fileName;
                
                Storage::disk('s3')->put($filePath, file_get_contents($file));
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
        $validFileTypesArray = ['jpeg','jpg','svg','png'];

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
        
        if ($fileNameExtension !== $file->getClientOriginalExtension()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INVALID_EXTENSION_OF_FILE'),
                trans('messages.custom_error_message.ERROR_NOT_VALID_IMAGE_FILE_EXTENSION')
            );
        }
        // If request parameter have any error
        if (!in_array($file->getClientOriginalExtension(), $validFileTypesArray) &&
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

        if (Storage::disk('s3')->exists($tenantName)) {
            if (!Storage::disk('s3')->exists($tenantName.'/assets/images/'.$fileName)) {
                throw new FileNotFoundException(
                    trans('messages.custom_error_message.ERROR_IMAGE_FILE_NOT_FOUND_ON_S3'),
                    config('constants.error_codes.ERROR_IMAGE_FILE_NOT_FOUND_ON_S3')
                );
            }
            // Upload file on s3
            Storage::disk('s3')->put(
                '/'.$tenantName.'/assets/images/'.$fileName,
                file_get_contents(
                    $file->getRealPath()
                )
            );
        } else {
            throw new BucketNotFoundException(
                trans('messages.custom_error_message.ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3'),
                config('constants.error_codes.ERROR_TENANT_ASSET_FOLDER_NOT_FOUND_ON_S3')
            );
        }
                  
        $apiStatus = Response::HTTP_OK;
        $apiMessage = "Image uploaded successfully";
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
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        exec('aws s3 cp --recursive s3://'.config('constants.AWS_S3_BUCKET_NAME').
            '/'.config('constants.AWS_S3_DEFAULT_THEME_FOLDER_NAME').'/'.
            env('AWS_S3_ASSETS_FOLDER_NAME').
            '/images s3://'.config('constants.AWS_S3_BUCKET_NAME').'/'
            .$tenantName.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').'/images');

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_ASSET_IMAGES_RESET_SUCCESS');
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
