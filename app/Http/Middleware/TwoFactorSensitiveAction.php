<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorSensitiveAction
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user || !$user->two_factor_enabled) {
            return $next($request);
        }
        
        // Verificar si ya se verificó 2FA para acciones sensibles en los últimos 10 minutos
        $lastSensitiveVerification = $request->session()->get('2fa_sensitive_verified_at');
        $tenMinutesAgo = now()->subMinutes(10);
        
        if (!$lastSensitiveVerification || $lastSensitiveVerification < $tenMinutesAgo) {
            if ($request->expectsJson()) {
                return response()->json([
                    'requires_2fa_verification' => true,
                    'message' => 'Esta acción requiere verificación de dos factores.'
                ], 200);
            }
            
            $request->session()->put('intended_sensitive_action', $request->fullUrl());
            
            return redirect()->route('2fa.verify.sensitive');
        }
        
        return $next($request);
    }
}
