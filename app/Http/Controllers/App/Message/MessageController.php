<?php
namespace App\Http\Controllers\App\Message;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Repositories\Message\MessageRepository;
use App\Traits\RestExceptionHandlerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $messageId = $this->messageRepository->store($request, config('constants.message.send_message_from.user'));

        // Set response data
        $apiStatus = Response::HTTP_CREATED;
        $apiMessage = trans('messages.success.MESSAGE_USER_MESSAGE_SEND_SUCESSFULLY');
        $apiData = ['message_id' => $messageId];

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * Get user's all messages data from admin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserMessages(Request $request): JsonResponse
    {
        $userMessages = $this->messageRepository->getUserMessages(
            $request,
            config('constants.message.send_message_from.admin'),
            [$request->auth->user_id]
        );
        
        $requestString = $request->except(['page','perPage']);
        $messagesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $userMessages,
            $userMessages->total(),
            $userMessages->perPage(),
            $userMessages->currentPage(),
            [
                'path' => $request->url().'?'.http_build_query($requestString),
                'query' => [
                    'page' => $userMessages->currentPage()
                ]
            ]
        );
        
        // generate responce data
        $apiData = $messagesPaginated->total()  > 0 ? $messagesPaginated : $userMessages;
        $apiStatus = Response::HTTP_OK;
        $apiMessage = ($messagesPaginated->total() > 0) ?
            trans('messages.success.MESSAGE_MESSAGES_ENTRIES_LISTING') :
            trans('messages.success.MESSAGE_NO_MESSAGES_ENTRIES_FOUND');
        
        return $this->responseHelper->successWithPagination(
            $apiStatus,
            $apiMessage,
            $apiData
        );
    }

    /**
     * Remove Message details.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $messageId
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, int $messageId): JsonResponse
    {
        try {
            $this->messageRepository->delete(
                $messageId,
                config('constants.message.send_message_from.admin'),
                $request->auth->user_id
            );
           
            // Set response data
            $apiStatus = Response::HTTP_NO_CONTENT;
            $apiMessage = trans('messages.success.MESSAGE_USER_MESSAGE_DELETED');
            
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_MESSAGE_USER_MESSAGE_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_MESSAGE_USER_MESSAGE_NOT_FOUND')
            );
        }
    }
}
