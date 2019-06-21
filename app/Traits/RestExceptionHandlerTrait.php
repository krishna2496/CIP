<?php
namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Illuminate\Database\QueryException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Console\Exception\CommandNotFoundException;

trait RestExceptionHandlerTrait
{
    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        dd($e);
        switch (true) {
            case $e instanceof ModelNotFoundException:
                $retval = $this->modelNotFound($e->getMessage());
                break;
            case $e instanceof InvalidArgumentException:
                $retval = $this->invalidArgument($e->getMessage());
                break;
            case $e instanceof PDOException:
                $retval = $this->PDO();
                break;
            case $e instanceof MethodNotAllowedHttpException:
                $retval = $this->MethodNotAllowedHttp();
                break;
            default:
                $retval = $this->badRequest($e->getMessage());
        }

        return $retval;
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message='Bad request')
    {
        return $this->jsonResponse(trans('messages.status_code.HTTP_STATUS_BAD_REQUEST'), trans('messages.status_type.HTTP_STATUS_TYPE_400'), $message);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($message = 'Record not found')
    {
        return $this->jsonResponse(trans('messages.status_code.HTTP_STATUS_NOT_FOUND'), trans('messages.status_type.HTTP_STATUS_TYPE_404'), $message);
    }
    
    /**
     * Returns json response for Invalid argument exception.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidArgument($message = 'Invalid argument')
    {
        return $this->jsonResponse(trans('messages.status_code.HTTP_STATUS_BAD_REQUEST'), trans('messages.status_type.HTTP_STATUS_TYPE_400'), $message);
    }
    
    /**
     * Returns json response for Query exception
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function PDO($message = 'Database operational error')
    {
        return $this->jsonResponse(trans('messages.status_code.HTTP_STATUS_BAD_GATEWAY'), trans('messages.status_type.HTTP_STATUS_TYPE_502'), $message);
    }
    
    /**
     * Returns json response for Methos not allowed http exception
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function MethodNotAllowedHttp($message = 'Method not allowed')
    {
        return $this->jsonResponse(trans('messages.status_code.HTTP_STATUS_METHOD_NOT_ALLOWED'), trans('messages.status_type.HTTP_STATUS_TYPE_405'), $message);
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(string $statusCode = '404', string $statusType = '', string $message = '')
    {
        return ResponseHelper::error($statusCode, $statusType, '', $message);
    }
}
