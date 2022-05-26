<?php

namespace App\Exceptions;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): ApiErrorResponse
    {
        if ($this->isHttpException($e)) {
            return new ApiErrorResponse($e->getMessage(), $e->getStatusCode());
        }
        if ($e instanceof UnauthorizedHttpException) {
            return new ApiErrorResponse($e->getMessage(), 401);
        }
        if ($e instanceof ApiValidationException) {
            return new ApiErrorResponse($e->getMessage());
        }
        Log::info($e);

        return new ApiErrorResponse($e->getMessage());
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
