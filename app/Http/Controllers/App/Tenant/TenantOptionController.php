<?php
namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TenantOption;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Repositories\TenantOption\TenantOptionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
use PDOException;
use Illuminate\Http\JsonResponse;
use Validator;

class TenantOptionController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var TenantOptionRepository
     */
    private $tenantOption;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

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
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper
     * @param App\Helpers\S3Helper
     * @return void
     */
    public function __construct(
        TenantOptionRepository $tenantOptionRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        Helpers $s3helper
    ) {
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
    }
    
    /**
     * Get tenant options from table `tenant_options`
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getTenantOption(Request $request): JsonResponse
    {
        $data = $optionData = $slider = array();

        try {
            // Find custom data
            $data = $this->tenantOptionRepository->getOptions();
            
            if ($data) {
                foreach ($data as $key => $value) {
                    // For slider
                    if ($value->option_name == config('constants.TENANT_OPTION_SLIDER')) {
                        $slider[]= json_decode($value->option_value, true);
                    } else {
                        $optionData[$value->option_name] = $value->option_value;
                    }
                }
                // Sort an array by sort order of slider
                if (!empty($slider)) {
                    $this->helpers->sortMultidimensionalArray($slider, 'sort_order', SORT_ASC);
                    $optionData['sliders'] = $slider;
                }
            }

            $tenantLanguages = $this->languageHelper->getTenantLanguages($request);

            if ($tenantLanguages->count() > 0) {
                foreach ($tenantLanguages as $key => $value) {
                    if ($value->default == 1) {
                        $optionData['defaultLanguage'] = strtoupper($value->code);
                        $optionData['defaultLanguageId'] = $value->language_id;
                    }
                    $optionData['language'][$value->language_id] = strtoupper($value->code);
                }
            }

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_OPTIONS_LIST');
            
            return $this->responseHelper->success($apiStatus, '', $optionData);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_DOMAIN_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND')
            );
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
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
     * Get tenant custom css from table `tenant_options`
     *
     * @return string
     */
    public function getCustomCss()
    {
        $tenantCustomCss = '';
        // find custom css
        try {
            $tenantOptions = $this->tenantOptionRepository->getOptionWithCondition(['option_name' => 'custom_css']);
            if ($tenantOptions) {
                $tenantCustomCss = $tenantOptions->option_value;
            }

            $apiData = ['custom_css' => $tenantCustomCss];
            $apiStatus = Response::HTTP_OK;

            return $this->responseHelper->success($apiStatus, '', $apiData);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
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
                "option_value" => "required"
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
            $tenantOption = $this->tenantOptionRepository->store($request->toArray());
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $tenantOption->toArray());
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans(
                    'messages.custom_error_message.ERROR_DATABASE_OPERATIONAL'
                )
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
     * Update tenant option value
     * 
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function updateTenantOption(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "option_name" => "required",
                "option_value" => "required"
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

            $updateData['option_value'] = $request->option_value;
            $tenantOption->update($updateData);

            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_TENANT_OPTION_UPDATED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $tenantOption->toArray());

        } catch (ModelNotFoundException $e) {            
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_OPTION_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_OPTION_NOT_FOUND')
            );            
        }
    }
}
