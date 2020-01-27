<?php
namespace App\Http\Controllers\App\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;

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
     * Create a new controller instance.
     *
     * @param  App\Helpers\S3Helper $s3helper
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(S3Helper $s3helper, Helpers $helpers)
    {
        $this->s3helper = $s3helper;
        $this->helpers = $helpers;
    }

    /**
    * Fetch language file
    *
    * @param \Illuminate\Http\Request $request
    * @param string $language
    * @return Array
    */
    public function fetchLanguageFile(Request $request, String $language) : array
    {
        $response = array();
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        try {
            $filePath = $this->s3helper->getLanguageFile($tenantName, $language);
        } catch (BucketNotFoundException $e) {
            throw $e;
        }

        $response['locale'] = $language;
        $response['data'] = json_decode(file_get_contents($filePath), true);
        return $response;
    }
}
