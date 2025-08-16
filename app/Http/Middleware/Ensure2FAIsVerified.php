<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Ensure2FAIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        // Si el usuario tiene 2FA activo y no ha verificado en esta sesión
        if ($user && $user->two_factor_enabled && !$request->session()->get('2fa_passed')) {
            return redirect()->route('2fa.verify');
        }
        return $next($request);
    }
}
