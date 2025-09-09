<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Add your custom logic if needed, like authentication or logging
        return $next($request);
    }
}
