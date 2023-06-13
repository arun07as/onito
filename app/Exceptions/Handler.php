<?php

namespace App\Exceptions;

use App\Enums\ErrorCodes;
use App\Traits\JsonResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use JsonResponses;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        /** @var \Illuminate\Http\Request $request */
        if ($request->wantsJson() || request()->is('api/*')) {
            return $this->convertExceptionToJsonResponse($e);
        }
        return parent::render($request, $e);
    }

    /**
     * Converts the exception to a json response
     *
     * @param \Throwable $e
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertExceptionToJsonResponse(Throwable $e)
    {
        $message = $e->getMessage();
        $statusCode = 500;
        $errors = [];
        $errorCode = ErrorCodes::INTERNAL_SERVER_ERROR;
        $headers = [];
        if ($e instanceof ValidationException) {
            $statusCode = 422;
            $errors = $e->validator->errors()->toArray();
            $message = $e->validator->errors()->first() ?? 'Invalid request data';
            $errorCode = ErrorCodes::VALIDATION_ERROR;
        } else if ($e instanceof ModelNotFoundException) {
            $statusCode = 404;
            $message = 'Resource not Found';
            $errorCode = ErrorCodes::RESOURCE_NOT_FOUND;
        } elseif ($e instanceof HttpExceptionInterface) {
            // If the exception is an instance of HtppException, then it may have headers
            // which were assigned by framework or by us
            $headers = $e->getHeaders();
            $statusCode = $e->getStatusCode();
            if ($e instanceof NotFoundHttpException) {
                $message = "Route not found";
                $errorCode = ErrorCodes::ROUTE_NOT_FOUND;
            } elseif ($e instanceof TooManyRequestsHttpException) {
                $errorCode = ErrorCodes::TOO_MANY_REQUESTS;
            } elseif ($statusCode == 503) {
                $message = "Server Under Maintenance. Please try again after some time";
                $errorCode = ErrorCodes::UNDER_MAINTENANCE;
            } else {
                $errorCode = null;
            }
        }
        if (app()->isProduction() && $statusCode == 500) {
            $message = "Technical Error. Please try again later";
        }
        return $this->sendError($message, $statusCode, $errors, $errorCode, $headers);
    }
}
