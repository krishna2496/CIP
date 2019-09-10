<?php
namespace App\Http\Controllers\Admin\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Slider\SliderRepository;
use App\Helpers\ResponseHelper;
use App\Helpers\S3Helper;
use App\Helpers\Helpers;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class SliderController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Slider\SliderRepository
     */
    private $sliderRepository;

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
     * @param App\Repositories\Slider\SliderRepository $sliderRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\Helpers $helpers
     * @param  App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        SliderRepository $sliderRepository,
        ResponseHelper $responseHelper,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
    }

    /**
     * Store slider details.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->toArray(),
            [
                "url" => "required|url|valid_media_path",
                "translations.*.lang" => "max:2",
                "sort_order" => "numeric|min:0"
            ]
        );

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
            $sliderCount = $this->sliderRepository->getAllSliderCount();

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
                $tenantName = $this->helpers->getSubDomainFromRequest($request);
                $imageUrl = "";
                if ($imageUrl = $this->s3helper->uploadFileOnS3Bucket($request->url, $tenantName)) {
                    $request->merge(['url' => $imageUrl]);
                    
                    // Create new slider
                    $slider = $this->sliderRepository->storeSlider($request->toArray());

                    // Set response data
                    $apiData = ['slider_id' => $slider->slider_id];
                    $apiStatus = Response::HTTP_CREATED;
                    $apiMessage = trans('messages.success.MESSAGE_SLIDER_ADD_SUCCESS');
                    return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
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
        } catch (TenantDomainNotFoundException $e) {
            throw $e;
        } catch (\ErrorException $e) {
            // Response error unable to upload file on S3
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_SLIDER_IMAGE_UPLOAD'),
                trans('messages.custom_error_message.ERROR_SLIDER_IMAGE_UPLOAD')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    /**
     * Update slider details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // Server side validataions
        $validator = Validator::make(
            $request->toArray(),
            [
                "url" => "sometimes|required|url",
                "translations.*.lang" => "max:2",
                "sort_order" => "numeric|min:0"
            ]
        );

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
            $this->sliderRepository->find($id);
            // Upload slider image on S3 server
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            
            if (isset($request->url)) {
                $imageUrl = "";
                if ($imageUrl = $this->s3helper->uploadFileOnS3Bucket($request->url, $tenantName)) {
                    $request->merge(['url' => $imageUrl]);
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

            // Update slider
            $this->sliderRepository->updateSlider($request->toArray(), $id);

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_SLIDER_UPDATED_SUCCESS');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (TenantDomainNotFoundException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_SLIDER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_SLIDER_NOT_FOUND')
            );
        } catch (\ErrorException $e) {
            // Response error unable to upload file on S3
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_SLIDER_IMAGE_UPLOAD'),
                trans('messages.custom_error_message.ERROR_SLIDER_IMAGE_UPLOAD')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
    
    /**
     * Get tenant slider
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $slider = $this->sliderRepository->getSliders();
            $apiStatus = Response::HTTP_OK;
            $apiMessage = ($slider->isEmpty()) ? trans('messages.success.MESSAGE_NO_SLIDER_FOUND') :
             trans('messages.success.MESSAGE_SLIDERS_LIST');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $slider->toArray());
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_TENANT_DOMAIN_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_TENANT_DOMAIN_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Delete slider.
     *
     * @param int $id
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->sliderRepository->delete($id);
            
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_SLIDER_DELETED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_SLIDER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_SLIDER_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
