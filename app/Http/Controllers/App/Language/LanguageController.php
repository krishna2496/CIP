<?php
namespace App\Http\Controllers\App\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;

//!  Language controller
/*!
This controller is responsible for handling language file listing operation.
 */
class LanguageController extends Controller
{
    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;
	
	/**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;
	
	/**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new controller instance.
     *
     * @param  App\Helpers\S3Helper $s3helper
     * @param  App\Helpers\Helpers $helpers
	 * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(S3Helper $s3helper, Helpers $helpers, LanguageHelper $languageHelper, ResponseHelper $responseHelper)
    {
        $this->s3helper = $s3helper;
        $this->helpers = $helpers;
		$this->languageHelper = $languageHelper;
		$this->responseHelper = $responseHelper;
    }

    /**
    * Fetch language file
    *
    * @param \Illuminate\Http\Request $request
    * @param string $language
    */
    public function fetchLanguageFile(Request $request, String $language)
    {
		// Check for valid language code
		if (!$this->languageHelper->isValidTenantLanguageCode($request, $language)) {
			return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_INVALID_CODE'),
                trans('messages.custom_error_message.ERROR_TENANT_LANGUAGE_INVALID_CODE')
            );
		}
		
		$response = array();
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
		
        $filePath = $this->s3helper->getLanguageFile($tenantName, $language);

        $response['locale'] = $language;
        $response['data'] = json_decode($this->helpers->removeUnwantedCharacters($filePath), true);
        return $response;
    }
}
