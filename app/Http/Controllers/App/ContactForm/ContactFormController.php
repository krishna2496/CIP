<?php
namespace App\Http\Controllers\App\ContactForm;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Repositories\ContactForm\ContactFormRepository;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactFormController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\ContactForm\ContactFormRepository;
     */
    private $contactFormRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new contact form controller instance
     *
     * @param App\Repositories\ContactForm\ContactFormRepository;
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        ContactFormRepository $contactFormRepository,
        ResponseHelper $responseHelper
    ) {
        $this->contactFormRepository = $contactFormRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Store contact form details
     *
     * @param \Illuminate\Http\Request $request     
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    { 
    	$validator = Validator::make(
            $request->toArray(),
            [
                'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11',
                'message' => 'required|max:60000',
            ]
        );
        
        // If validator fails
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_CONTACT_FORM_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        
        // Store contact form data
        $contactFormData = $this->contactFormRepository->store($request);

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.CONTACT_FORM_ADDED_SUCESSFULLY');
        $apiData = ['contact_form_id' => $contactFormData->contact_form_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
