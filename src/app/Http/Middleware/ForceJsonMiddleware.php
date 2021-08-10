<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('*api*')) {
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('Content-Type', 'application/json');
        }

        return $next($request);
    }
}
