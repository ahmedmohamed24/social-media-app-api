<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AccessDeniedHttpException) {
            return response()->json(['data' => [], 'errors' => ['User Not authorized to perform this action'], 'meta' => ['message' => $e->getMessage()]], 403);
        }
        if ($e instanceof AuthenticationException) {
            return response()->json(['data' => [], 'errors' => ['Unauthenticated'], 'meta' => ['message' => $e->getMessage()]], 401);
        }
        if ($e instanceof ValidationException) {
            return response()->json(['data' => [], 'errors' => [$e->errors()], 'meta' => ['message' => $e->getMessage()]], 401);
        }
        if ($e instanceof ModelNotFoundException) {
            return response()->json(['data' => [], 'errors' => ['Model' => 'Not Found!'], 'meta' => ['message' => $e->getMessage()]], 404);
        }
        if ($e instanceof UnauthorizedException) {
            return response()->json(['data' => [], 'errors' => ['User Not authorized to perform this action'], 'meta' => ['message' => $e->getMessage()]], 403);
        }

        if ($e instanceof UnauthorizedException) {
            return response()->json(['data' => [], 'errors' => ['User Not authorized to perform this action'], 'meta' => ['message' => $e->getMessage()]], 403);
        }

        return parent::render($request, $e);
    }
}
