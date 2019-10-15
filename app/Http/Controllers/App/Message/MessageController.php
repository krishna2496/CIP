<?php
namespace App\Http\Controllers\App\Message;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Repositories\Message\MessageRepository;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class MessageController extends Controller
{
    use RestExceptionHandlerTrait;
    /**
     * @var App\Repositories\Message\MessageRepository;
     */
    private $messageRepository;

    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new message controller instance
     *
     * @param App\Repositories\Message\MessageRepository;
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(
        MessageRepository $messageRepository,
        ResponseHelper $responseHelper
    ) {
        $this->messageRepository = $messageRepository;
        $this->responseHelper = $responseHelper;
    }

    /**
     * Send message to admin
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->toArray(),
            [
                'subject' => 'required|max:255',
                'message' => 'required|max:60000'
            ]
        );
        
        // If validator fails
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_MESSAGE_REQUIRED_FIELDS_EMPTY'),
                $validator->errors()->first()
            );
        }
        
        // Store message data
        $messageData = $this->messageRepository->store($request, config('constants.send_message_from.user'));

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_SEND_SUCESSFULLY');
        $apiData = ['message_id' => $messageData->message_id];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
