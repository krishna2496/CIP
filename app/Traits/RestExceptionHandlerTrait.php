<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;

trait RestExceptionHandlerTrait
{
    private $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest(string $message = 'Bad request')
    {
        return $this->jsonResponse(
            Response::HTTP_BAD_REQUEST,
            Response::$statusTexts[Response::HTTP_BAD_REQUEST],
            '',
            $message
        );
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound(string $customErrorCode = '', string $message = 'Record not found')
    {
        return $this->jsonResponse(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            $customErrorCode,
            $message
        );
    }
    
    /**
     * Returns json response for Invalid argument exception.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidArgument(string $customErrorCode = '', string $message = 'Invalid argument')
    {
        return $this->jsonResponse(
            Response::HTTP_BAD_REQUEST,
            Response::$statusTexts[Response::HTTP_BAD_REQUEST],
            $customErrorCode,
            $message
        );
    }
    
    /**
     * Returns json response for Query exception
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function PDO(string $customErrorCode = '', string $message = 'Database operational error')
    {
        return $this->jsonResponse(
            Response::HTTP_BAD_GATEWAY,
            Response::$statusTexts[Response::HTTP_BAD_GATEWAY],
            $customErrorCode,
            $message
        );
    }
    
    /**
     * Returns json response for Methos not allowed http exception
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function methodNotAllowedHttp(string $message = 'Method not allowed')
    {
        return $this->jsonResponse(
            Response::HTTP_METHOD_NOT_ALLOWED,
            Response::$statusTexts[Response::HTTP_METHOD_NOT_ALLOWED],
            '',
            $message
        );
    }

    /**
     * Returns json response for AWS S3 exception
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function s3Exception(string $customErrorCode = '', string $message = 'Internal Server Error')
    {
        return $this->jsonResponse(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
            $customErrorCode,
            $message
        );
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(
        string $statusCode = '404',
        string $statusType = '',
        string $customErrorCode = '',
        string $message = ''
    ) {
        return $this->responseHelper->error($statusCode, $statusType, $customErrorCode, $message);
    }
}
