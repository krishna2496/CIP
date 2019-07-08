<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Traits\RestExceptionHandlerTrait;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FileDownloadException;

class Handler extends ExceptionHandler
{
    use RestExceptionHandlerTrait;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof FileNotFoundException) {
            return $this->filenotFound($exception->getCode(), $exception->getMessage());
        }
        if ($exception instanceof FileDownloadException) {
            return $this->fileDownloadError($exception->getCode(), $exception->getMessage());
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->methodNotAllowedHttp();
        }
        return $this->badRequest();
    }
}
