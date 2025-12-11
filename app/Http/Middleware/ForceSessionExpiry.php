<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForceSessionExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        // If session cookie doesn't exist or is expired
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            // Logout user if they're logged in without "remember me"
            if (Auth::check() && !$request->hasCookie(Auth::getRecallerName())) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }
        
        return $next($request);
    }
}