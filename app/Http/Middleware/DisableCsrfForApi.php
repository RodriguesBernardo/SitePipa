<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCsrfForApi
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->path(), 'api/')) {
            config(['session.driver' => 'array']);
        }
        
        return $next($request);
    }
}