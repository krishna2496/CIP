<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;

trait RestExceptionHandlerTrait
{
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new trait instance.
     *
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
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
     * @param string $customErrorCode
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
     * @param string $customErrorCode
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
     * @param string $customErrorCode
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
     * Returns json response for internal server error.
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function internaServerError(string $customErrorCode = '', string $message = 'Internal server error')
    {
        return $this->jsonResponse(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
            $customErrorCode,
            $message
        );
    }

    /**
     * Returns json response for file download error.
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function fileDownloadError(string $customErrorCode = '', string $message = 'Failed to download files')
    {
        return $this->jsonResponse(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            $customErrorCode,
            $message
        );
    }

    /**
     * Returns json response for failed to compile scss files
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function parserError(string $customErrorCode = '', string $message = 'Failed to compile SCSS files')
    {
        return $this->jsonResponse(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            $customErrorCode,
            $message
        );
    }
    
    /**
     * Returns json response for failed to perform S3 opration
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function s3Exception(string $customErrorCode = '', string $message = 'S3 exception')
    {
        return $this->jsonResponse(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            $customErrorCode,
            $message
        );
    }

    /**
     * Returns json response for bucket not found on s3
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function bucketNotFound(string $customErrorCode = '', string $message = 'Assets bucket not found on S3')
    {
        return $this->jsonResponse(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            $customErrorCode,
            $message
        );
    }

    /**
     * Returns json response for files not found on s3 for bucket folder
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function fileNotFound(string $customErrorCode = '', string $message = 'File not found on S3')
    {
        return $this->jsonResponse(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
            $customErrorCode,
            $message
        );
    }
    
    /**
     * Returns json response for tenant's domain not found
     *
     * @param string $customErrorCode
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function tenantDomainNotFound(string $customErrorCode = '', string $message = 'Tenant Domain not found')
    {
        return $this->jsonResponse(
            Response::HTTP_NOT_FOUND,
            Response::$statusTexts[Response::HTTP_NOT_FOUND],
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
