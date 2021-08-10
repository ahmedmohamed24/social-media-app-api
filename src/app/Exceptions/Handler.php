<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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
        if ($e instanceof AuthenticationException) {
            return response()->json(['data' => [], 'errors' => ['Unauthenticated'], 'meta' => ['message' => $e->getMessage()]], 401);
        }
        if ($e instanceof ValidationException) {
            return response()->json(['data' => [], 'errors' => [$e->errors()], 'meta' => ['message' => $e->getMessage()]], 401);
        }

        return parent::render($request, $e);
    }
}
