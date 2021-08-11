<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;

class ForceJsonMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET')) {
            return $next($request);
        }
        if ($request->isJson()) {
            return $next($request);
        }
        if ('multipart/form-data' === \substr($request->header('Content-Type'), 0, \strpos($request->header('Content-Type'), ';'))) {
            $request->setJson(\json_encode($request->all()));

            return $next($request);
        }

        return $this->response(403, 'unsupported type', ['send data in a JSON format'], \null);
    }
}
