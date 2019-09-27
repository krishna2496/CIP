<?php
namespace App\Http\Controllers\App\Tenant;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\TenantOption;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\Slider\SliderRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\RestExceptionHandlerTrait;
use InvalidArgumentException;
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
     * @var SliderRepository
     */
    private $sliderRepository;

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
     * Create a new controller instance.
     *
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param App\Repositories\Slider\SliderRepository $sliderRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        TenantOptionRepository $tenantOptionRepository,
        SliderRepository $sliderRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers
    ) {
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->sliderRepository = $sliderRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
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

        // Find custom data
        $data = $this->tenantOptionRepository->getOptions();
        
        if ($data) {
            foreach ($data as $key => $value) {
                $optionData[$value->option_name] = $value->option_value;
            }
        }
        // For slider
        $sliderData = array();
        $sliders = $this->sliderRepository->getAllSliders();
        foreach ($sliders as $key => $value) {
            $value['slider_detail']['translations'] = $value['translations'];
            unset($value['translations']);
            $sliderData[] = $value;
        }
        $optionData['sliders'] = $sliderData;

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
    }

    /**
     * Get tenant custom css from table `tenant_options`
     *
     * @return JsonResponse
     */
    public function getCustomCss(): JsonResponse
    {
        $tenantCustomCss = '';
        // find custom css
        $tenantOptions = $this->tenantOptionRepository->getOptionWithCondition(['option_name' => 'custom_css']);
        if ($tenantOptions) {
            $tenantCustomCss = $tenantOptions->option_value;
        }

        $apiData = ['custom_css' => $tenantCustomCss];
        $apiStatus = Response::HTTP_OK;

        return $this->responseHelper->success($apiStatus, '', $apiData);
    }
    
    /**
     * Display tenant option value
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function fetchTenantOptionValue(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            [
                "option_name" => "required"
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
        
        // Fetch tenant option value
        $tenantOptionDetail = $this->tenantOptionRepository->getOptionValue($request->option_name);
        $apiMessage = ($tenantOptionDetail->isEmpty())
        ? trans('messages.custom_error_message.ERROR_TENANT_OPTION_NOT_FOUND')
        : trans('messages.success.MESSAGE_TENANT_OPTION_FOUND');
        $apiStatus = Response::HTTP_OK;
        
        return $this->responseHelper->success($apiStatus, $apiMessage, $tenantOptionDetail->toArray());
    }
}
