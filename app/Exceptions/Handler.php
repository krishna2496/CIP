<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Traits\RestExceptionHandlerTrait;
use Leafo\ScssPhp\Exception\ParserException;
use App\Exceptions\BucketNotFoundException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\TenantDomainNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->methodNotAllowedHttp();
        }
        // @codeCoverageIgnoreStart
        if ($exception instanceof ParserException) {
            return $this->parserError($exception->getCode(), $exception->getMessage());
        }
        if ($exception instanceof BucketNotFoundException) {
            return $this->bucketNotFound($exception->getCode(), $exception->getMessage());
        }
        if ($exception instanceof FileNotFoundException) {
            return $this->filenotFound($exception->getCode(), $exception->getMessage());
        }
        // @codeCoverageIgnoreEnd
        if ($exception instanceof TenantDomainNotFoundException) {
            return $this->tenantDomainNotFound($exception->getCode(), $exception->getMessage());
        }
        return $this->internalServerError(trans('messages.custom_error_message.ERROR_INTERNAL_SERVER_ERROR'));
    }
}
