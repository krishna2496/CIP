<?php
namespace App\Http\Controllers\Admin\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\BucketNotFoundException;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use Validator;
use App\Exceptions\TenantDomainNotFoundException;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\FileNotFoundException;
use App\Events\User\UserActivityLogEvent;
use App\Helpers\LanguageHelper;

//!  Language controller
/*!
This controller is responsible for handling language file listing operation.
 */
class LanguageController extends Controller
{
    use RestExceptionHandlerTrait;
   
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var string
     */
    private $userApiKey;

    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @param Illuminate\Http\Request $request
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(
        ResponseHelper $responseHelper,
        Helpers $helpers,
        S3Helper $s3helper,
        Request $request,
        LanguageHelper $languageHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
        $this->userApiKey = $request->header('php-auth-user');
        $this->languageHelper = $languageHelper;
    }

    /**
     * Fetch language file url.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchLanguageFile(Request $request): JsonResponse
    {
        // Server side validations
        $validator = Validator::make(
            $request->toArray(),
            [
                "code" => "required|max:2|min:2"
            ]
        );
        
        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_INVALID_CODE'),
                $validator->errors()->first()
            );
        }
        
        // Check for valid language code
        if (!$this->languageHelper->getTenantLanguageByCode($request, $request->code)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_INVALID_CODE'),
                trans('messages.custom_error_message.ERROR_TENANT_LANGUAGE_INVALID_CODE')
            );
        }
        
        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        // Fetch language file url
        $languageFileUrl = $this->s3helper->getLanguageFile($tenantName, $request->code);
        $apiData = ["file_path" => $languageFileUrl];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_TENANT_LANGUAGE_FILE_FOUND');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
        
    /**
     * It will update language file on S3
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadLanguageFile(Request $request): JsonResponse
    {
        // Server side validations
        $validator = Validator::make(
            $request->toArray(),
            [
                "file_name" => "required|max:2|min:2",
                "file_path" => "required"
            ]
        );

        // If post parameter have any missing parameter
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_FILE_UPLOAD_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        $file = $request->file('file_path');
        $fileName = $request->file_name;
        $fileExtension = pathinfo($file->getClientOriginalName())['extension'];

        $validFileType = ['text/plain'];
        // If request parameter have any error
        if (!in_array($file->getMimeType(), $validFileType) ||
        ('.'.$fileExtension !== config('constants.AWS_S3_LANGUAGE_FILE_EXTENSION'))) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_NOT_VALID_TENANT_LANGUAGE_FILE_EXTENSION'),
                trans('messages.custom_error_message.ERROR_NOT_VALID_TENANT_LANGUAGE_FILE_EXTENSION')
            );
        }

        // Validate json file data
        if (json_decode(file_get_contents($file->getRealPath())) === null) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_INVALID_JSON_FORMAT'),
                trans('messages.custom_error_message.ERROR_TENANT_LANGUAGE_INVALID_JSON_FORMAT')
            );
        }
        
        // Check for valid language code
        if (!$this->languageHelper->getTenantLanguageByCode($request, $fileName)) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_TENANT_LANGUAGE_INVALID'),
                trans('messages.custom_error_message.ERROR_TENANT_LANGUAGE_INVALID')
            );
        }

        // Get domain name from request and use as tenant name.
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        //Get default file from url
        $defaultFileUrl = $this->s3helper->getDefaultLanguageFile($fileName);
        $defaultFileContent = json_decode(file_get_contents($defaultFileUrl));
        $keyNotExists = array();
        $keyValueNotExists = array();
        $missingKeyValueString = '';
        $userLanguageFile = json_decode(file_get_contents($file->getRealPath()));
        //Code to check file keywords
        foreach ($defaultFileContent as $index => $data) {
            if (isset($userLanguageFile->$index)) {
                foreach ($defaultFileContent->$index as $key => $value) {
                    if (!array_key_exists($key, $userLanguageFile->$index)) {
                        $keyNotExists[] = $key;
                    } elseif (trim($userLanguageFile->$index->$key) === "") {
                        $keyValueNotExists[] = $key;
                    }
                }
            } else {
                $keyNotExists[] = $index;
            }
        }

        if (!empty($keyNotExists)) {
            $missingKeyValueString.= ' MISSING_KEYS: '.implode(", ", $keyNotExists).'.';
        }
        if (!empty($keyValueNotExists)) {
            $missingKeyValueString.= ' MISSING_VALUES: '.implode(",", $keyValueNotExists);
        }
        if (isset($missingKeyValueString) && $missingKeyValueString !== '') {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_INCOMPLETE_LANGUAGE_FILE'),
                trans('messages.custom_error_message.ERROR_INCOMPLETE_LANGUAGE_FILE').$missingKeyValueString
            );
        }

        //Upload file on S3
        set_time_limit(0);
        $context = stream_context_create(array('http'=> array(
            'timeout' => 1200
        )));
        
        $disk = Storage::disk('s3');
        $documentName = $fileName . '.' . $fileExtension;
        $documentPath =  $tenantName .'/'.config('constants.AWS_S3_LANGUAGES_FOLDER_NAME').'/' . $documentName;
        $disk->put($documentPath, @file_get_contents($file, false, $context));
        $pathInS3 = 'https://' . env('AWS_S3_BUCKET_NAME') . '.s3.'
        . env("AWS_REGION") . '.amazonaws.com/' . $documentPath;

        $fileDetail = array();
        $fileDetail['file_name'] = $documentName;

        // Make activity log
        event(new UserActivityLogEvent(
            config('constants.activity_log_types.TENANT_LANGUAGE'),
            config('constants.activity_log_actions.UPDATED'),
            config('constants.activity_log_user_types.API'),
            $this->userApiKey,
            get_class($this),
            $fileDetail,
            null,
            null
        ));
        $apiData = ["file_path" => $pathInS3];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_TENANT_LANGUAGE_UPDATED_SUCESSFULLY');
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
