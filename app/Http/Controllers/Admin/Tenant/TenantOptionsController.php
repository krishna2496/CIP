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
use PDOException;
use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use App\Jobs\CreateFolderInS3BucketJob;
use App\Traits\RestExceptionHandlerTrait;
use App\Exceptions\BucketNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileUploadException;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }

    /**
     * Store slider details.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSlider(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), ["url" => "required"]);

        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_SLIDER_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        try {
            // Get total count of "slider"
            $sliderCount = $this->tenantOptionRepository->getAllSlider()->count();

            // Prevent data insertion if user is trying to insert more than defined slider limit records
            if ($sliderCount >= config('constants.SLIDER_LIMIT')) {
                // Set response data
                return $this->responseHelper->error(
                    Response::HTTP_FORBIDDEN,
                    Response::$statusTexts[Response::HTTP_FORBIDDEN],
                    config('constants.error_codes.ERROR_SLIDER_LIMIT'),
                    trans('messages.custom_error_message.ERROR_SLIDER_LIMIT')
                );
            } else {
                // Upload slider image on S3 server
                try {
                    // Get domain name from request and use as tenant name.
                    $tenantName = $this->helpers->getSubDomainFromRequest($request);
                } catch (\Exception $e) {
                    return $this->badRequest($e->getMessage());
                }
                if ($request->url = $this->s3helper->uploadFileOnS3Bucket($request->url, $tenantName)) {
                    // Set data for create new record
                    $insertData = array();
                    $insertData['option_name'] = config('constants.TENANT_OPTION_SLIDER');
                    $insertData['option_value'] = serialize(json_encode($request->toArray()));

                    // Create new tenant_option
                    $tenantOption = $this->tenantOptionRepository->storeSlider($insertData);

                    // Set response data
                    $apiStatus = Response::HTTP_OK;
                    $apiMessage = trans('messages.success.MESSAGE_SLIDER_ADD_SUCCESS');
                    return $this->responseHelper->success($apiStatus, $apiMessage);
                } else {
                    // Response error unable to upload file on S3
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_SLIDER_IMAGE_UPLOAD'),
                        trans('messages.custom_error_message.ERROR_SLIDER_IMAGE_UPLOAD')
                    );
                }
            }
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
     * Reset to default style
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetStyleSettings(Request $request): JsonResponse
    {
        try {
            // Get domain name from request and use as tenant name.
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
        } catch (\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
        
        try {
            // Copy default theme folder to tenant folder on s3
            dispatch(new CreateFolderInS3BucketJob($tenantName));
        } catch (S3Exception $e) {
            return $this->s3Exception(
                config('constants.error_codes.ERROR_FAILED_TO_RESET_STYLING'),
                trans('messages.custom_error_message.ERROR_FAILED_TO_RESET_STYLING')
            );
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.ERROR_FAILED_TO_RESET_STYLING'));
        }

        // Copy tenant folder to local
        dispatch(new DownloadAssestFromS3ToLocalStorageJob($tenantName));
        
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

        try {
            $this->tenantOptionRepository->updateStyleSettings($request);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
            );
        } catch (\ErrorException $e) {
            return $this->internaServerError(
                config('constants.error_codes.ERROR_ON_UPDATING_STYLING_VARIBLE_IN_DATABASE'),
                trans(
                    'messages.custom_error_message.ERROR_ON_UPDATING_STYLING_VARIBLE_IN_DATABASE'
                )
            );
        } catch (\Exception $e) {
            return $this->badRequest('messages.custom_error_message.ERROR_OCCURRED');
        }

        $file = $request->file('custom_scss_files');

        try {
            // Get domain name from request and use as tenant name.
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
        } catch (\Exception $e) {
            return $this->badRequest($e->getMessage());
        }

        // Need to check local copy for tenant assest is there or not?
        if (!Storage::disk('local')->exists($tenantName)) {
            // Copy files from S3 and download in local storage using tenant FQDN
            dispatch(new DownloadAssestFromS3ToLocalStorageJob($tenantName));
        }

        if ($request->hasFile('custom_scss_files')) {
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
                $fileName = $file->getClientOriginalName();

                /* Check user uploading custom style variable file,
                then we need to make it as high priority instead of passed colors. */
                
                if ($fileName === env('CUSTOM_STYLE_VARIABLE_FILE_NAME')) {
                    $isVariableScss = 1;
                }

                if (Storage::disk('local')->exists('/'.$tenantName.'/assets/scss/'.$fileName)) {
                    // Delete existing one
                    Storage::disk('local')->delete($file);
                } else {
                    // Error: Return like uploaded file name doesn't match with structure.
                    return $this->responseHelper->error(
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                        config('constants.error_codes.ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE'),
                        trans('messages.custom_error_message.ERROR_FILE_NAME_NOT_MATCHED_WITH_STRUCTURE')
                    );
                }

                if (!Storage::disk('local')->put(
                    '/'.$tenantName.'/assets/scss/'.$fileName,
                    file_get_contents($file->getRealPath())
                )) {
                    // Error unable to download file to server
                    throw new FileUploadException(
                        trans('messages.custom_error_message.ERROR_DOWNLOADING_IMAGE_TO_LOCAL'),
                        config('constants.error_codes.ERROR_DOWNLOADING_IMAGE_TO_LOCAL')
                    );
                }
            }
        }

        // Compile scss and upload on s3 css folder in tenant's folder
        $options['isVariableScss'] = $isVariableScss;
        
        if (isset($request->primary_color) && $request->primary_color!='') {
            $options['primary_color'] = $request->primary_color;
        }

        if (isset($request->secondary_color) && $request->secondary_color!='') {
            $options['secondary_color'] = $request->secondary_color;
        }
                    
        $this->s3helper->compileLocalScss($tenantName, $options);

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
        try {
            // Get domain name from request and use as tenant name.
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
        } catch (\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
        
        $assetFilesArray = $this->s3helper->getAllScssFiles($tenantName);
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
        // Server side validataions
        $validator = Validator::make($request->toArray(), ["image_file" => "required"]);

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
        $fileName = $file->getClientOriginalName();
        
        try {
            // Get domain name from request and use as tenant name.
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
        } catch (\Exception $e) {
            return $this->badRequest($e->getMessage());
        }
        
        if (Storage::disk('s3')->exists($tenantName)) {
            if (!Storage::disk('s3')->exists($tenantName.'/assets/images/'.$fileName)) {
                throw new FileNotFoundException(
                    trans('messages.custom_error_message.ERROR_IMAGE_FILE_NOT_FOUND_ON_S3'),
                    config('constants.error_codes.ERROR_IMAGE_FILE_NOT_FOUND_ON_S3')
                );
            }
            // Upload file on s3
            if (!Storage::disk('s3')->put(
                '/'.$tenantName.'/assets/images/'.$fileName,
                file_get_contents($file->getRealPath())
            )) {
                throw new FileUploadException(
                    trans('messages.custom_error_message.ERROR_WHILE_UPLOADING_IMAGE_ON_S3'),
                    config('constants.error_codes.ERROR_WHILE_UPLOADING_IMAGE_ON_S3')
                );
            }
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
}
