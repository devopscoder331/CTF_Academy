<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveXsrfCookie
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Remove XSRF-TOKEN cookie from response
        $response->headers->removeCookie('XSRF-TOKEN');
        
        return $response;
    }
}

