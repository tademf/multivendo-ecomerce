<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ይህ መስመር መኖር አለበት ስህተቱን ለማጥፋት

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Auth::check() እና Auth::user() የሚባሉት የሚሰሩት ከላይ Import ከተደረጉ ብቻ ነው
        if (!Auth::check() || !Auth::user()->is_verified) {
            
            // በ Inertia ከሆነ ወደ Verification ገጽ Redirect ያደርጋል
            return redirect()->route('verification.request')
                ->with('error', 'ይህንን ገጽ ለመጠቀም መጀመሪያ መረጋገጥ አለብዎት።');
        }

        return $next($request);
    }
}