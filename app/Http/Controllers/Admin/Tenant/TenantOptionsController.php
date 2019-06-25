<?php
namespace App\Http\Controllers\Admin\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\TenantOption\TenantOptionRepository;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ResponseHelper;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;
use Validator;
use PDOException;
use App\Jobs\DownloadAssestFromS3ToLocalStorageJob;
use App\Jobs\CreateFolderInS3BucketJob;

class TenantOptionsController extends Controller
{
    /**
     * @var App\Repositories\TenantOption\TenantOptionRepository
     */
    private $tenantOptionRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param  App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(TenantOptionRepository $tenantOptionRepository, ResponseHelper $responseHelper)
    {
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->responseHelper = $responseHelper;
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
     * @return mixed response
     */
    public function storeSlider(Request $request)
    {
        // Server side validataions
        $validator = Validator::make($request->toArray(), ["url" => "required"]);

        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_20018'),
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
                    trans('messages.status_code.HTTP_STATUS_FORBIDDEN'),
                    trans('messages.status_type.HTTP_STATUS_TYPE_403'),
                    trans('messages.custom_error_code.ERROR_40020'),
                    trans('messages.custom_error_message.40020')
                );
            } else {
                // Upload slider image on S3 server
                $tenantName = Helpers::getSubDomainFromRequest($request);
                if ($request->url = S3Helper::uploadFileOnS3Bucket($request->url, $tenantName)) {
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
                        trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                        trans('messages.custom_error_code.ERROR_40022'),
                        trans('messages.custom_error_message.40022')
                    );
                }
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Reset to default style
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function resetStyleSettings(Request $request)
    {
        try {
            // Get domain name from request and use as tenant name.
            $tenantName = Helpers::getSubDomainFromRequest($request);
            
            // Copy default theme folder to tenant folder on s3
            dispatch(new CreateFolderInS3BucketJob($tenantName));

            // Copy tenant folder to local
            dispatch(new DownloadAssestFromS3ToLocalStorageJob($tenantName));

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_CUSTOM_STYLE_RESET_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (\Exception $e) {
        }
    }

    /**
     * Update tenant custom styling data: primary color, secondary color and custom css
     *
     * @param \Illuminate\Http\Request $request
     * @return mix
     */
    public function updateStyleSettings(Request $request)
    {
        $isVariableScss = 0;

        $this->tenantOptionRepository->updateStyleSettings($request);
        
        $file = $request->file('custom_scss_files');

        // Get domain name from request and use as tenant name.
        $tenantName = Helpers::getSubDomainFromRequest($request);

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
                    trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                    trans('messages.custom_error_code.ERROR_20044'),
                    trans('messages.custom_error_message.20044')
                );
            }
            
            if ($file->isValid()) {
                $fileName = $file->getClientOriginalName();

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
                        trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                        trans('messages.custom_error_code.ERROR_20040'),
                        trans('messages.custom_error_message.20040')
                    );
                }

                if (!Storage::disk('local')->put(
                    '/'.$tenantName.'/assets/scss/'.$fileName,
                    file_get_contents($file->getRealPath())
                )) {
                    // Error unable to download file to server
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
                    
        S3Helper::compileLocalScss($tenantName, $options);

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_CUSTOM_STYLE_UPLOADED_SUCCESS');
        return $this->responseHelper->success($apiStatus, $apiMessage);
    }
}
