<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle($request, Closure $next)
    {

        if (!session('login')) {
            return redirect('/login')->with('error', 'Silakan login dulu');
        }

        return $next($request);
    }

    
}
