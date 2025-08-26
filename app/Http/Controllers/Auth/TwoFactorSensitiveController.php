<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSensitiveController extends Controller
{
    /**
     * Mostrar el formulario de verificación 2FA para acciones sensibles
     */
    public function show(Request $request)
    {
        if (!Auth::user()->two_factor_enabled) {
            return redirect()->route('dashboard');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'view' => view('auth.2fa-sensitive-modal')->render(),
                'requires_2fa_verification' => true
            ]);
        }
        
        return view('auth.2fa-sensitive');
    }
    
    /**
     * Verificar el código 2FA para acciones sensibles
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:6|max:6'
        ]);
        
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        if (!$user->two_factor_secret || !$google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Código incorrecto. Intenta nuevamente.'
                ], 422);
            }
            
            return back()->withErrors(['code' => 'Código incorrecto. Intenta nuevamente.']);
        }
        
        // Marcar como verificado por 10 minutos
        $request->session()->put('2fa_sensitive_verified_at', now());
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Verificación exitosa.',
                'redirect' => $request->session()->get('intended_sensitive_action', route('dashboard'))
            ]);
        }
        
        // Redirigir a la acción original o al dashboard
        return redirect()->to($request->session()->get('intended_sensitive_action', route('dashboard')));
    }
}
