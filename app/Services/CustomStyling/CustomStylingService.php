<?php
namespace App\Services\CustomStyling;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exceptions\TenantDomainNotFoundException;
use App\Exceptions\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helpers;
use Validator;
use App\Helpers\ResponseHelper;

class CustomStylingService
{
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(Helpers $helpers, ResponseHelper $responseHelper)
    {
        $this->helpers = $helpers;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Upload file on S3 and validate it
     *
     * @param Illuminate\Http\Request $request
     * @return null
     */
    public function uploadFileOnS3(Request $request)
    {
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $file = $request->file('image_file');
        $fileName = $request->image_name;

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
            ),
            [
                'mimetype' => $file->getMimeType()
            ]
        );
        return null;
    }

    /**
     * It will upload image on S3 after check validations
     *
     * @param Illuminate\Http\Request $request
     * @return Null|JsonResponse
     */
    public function uploadImage(Request $request): ?JsonResponse
    {
        $tenantName = $this->helpers->getSubDomainFromRequest($request);

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
        if ($file->getClientOriginalExtension() != "scss") {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_NOT_VALID_EXTENSION'),
                trans('messages.custom_error_message.ERROR_NOT_VALID_EXTENSION')
            );
        }

        if ($file->isValid()) {
            $fileName = $request->custom_scss_file_name;

            // if it is not exist then need to throw error
            if (!Storage::disk('s3')->exists($tenantName.'/assets/scss/'.$fileName)) {
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

            $filePath = $tenantName.'/'.env('AWS_S3_ASSETS_FOLDER_NAME').'/'.
            config('constants.AWS_S3_SCSS_FOLDER_NAME').'/'. $fileName;

            Storage::disk('s3')->put($filePath, file_get_contents($file));
        }
        return null;
    }

    /**
     * It will check uploading file validation
     *
     * @param Illuminate\Http\Request $request
     * @return Null|JsonResponse
     */
    public function checkFileValidations(Request $request): ?JsonResponse
    {
        $validFileTypesArray = ['image/jpeg','image/svg+xml','image/png'];

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
        if (!in_array($file->getMimeType(), $validFileTypesArray) &&
        $fileNameExtension === $file->getClientOriginalExtension()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_NOT_VALID_EXTENSION'),
                trans('messages.custom_error_message.ERROR_NOT_VALID_IMAGE_FILE_EXTENSION')
            );
        }
        return null;
    }
}
