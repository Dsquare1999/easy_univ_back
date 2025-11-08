<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRoute
{
    public function handle($request, Closure $next)
    {
        Log::info('Route called: ' . $request->fullUrl());
        Log::info('Method: ' . $request->method());
        Log::info('Input:', $request->all());
        
        return $next($request);
    }
}