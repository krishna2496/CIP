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

class TenantOptionController extends Controller
{
    /**
     * @var TenantOptionRepository
     */
    private $tenantOption;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\TenantOption\TenantOptionRepository $tenantOptionRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(TenantOptionRepository $tenantOptionRepository, ResponseHelper $responseHelper)
    {
        $this->tenantOptionRepository = $tenantOptionRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Get tenant options from table `tenant_options`
     *
     * @return mixed
     */
    public function getTenantOption(Request $request)
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
                    Helpers::sortMultidimensionalArray($slider, 'sort_order', SORT_ASC);
                    $optionData['sliders'] = $slider;
                }
            }

            $tenantLanguages = LanguageHelper::getTenantLanguages($request);

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
            $apiMessage = trans('messages.success.MESSAGE_USER_CREATED');
            
            return $this->responseHelper->success($apiStatus, '', $optionData);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }

    /**
     * Get tenant logo from table `tenant_options`
     *
     * @return string
     */
    public function getTenantLogo()
    {
        $tenantLogo = '';

        try {
            // find custom data
            $tenantOptions = $this->tenantOptionRepository->getOptionWithCondition(['option_name', 'custom_logo']);
            if ($tenantOptions && $tenantOptions->option_value) {
                $tenantLogo = $tenantOptions->option_value;
            }
            return $tenantLogo;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
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
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception(trans('messages.custom_error_message.999999'));
        }
    }
}
