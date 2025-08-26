<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function show()
    { /* returns QR code view – omitted for brevity */
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:6|max:6'
        ]);
        
        $secret = decrypt(session('2fa_secret'));
        $g2fa = new Google2FA();
        
        if ($g2fa->verifyKey($secret, $request->code)) {
            $userId = Auth::id();
            DB::table('users')->where('id', $userId)->update([
                'two_factor_secret' => encrypt($secret), 
                'two_factor_enabled' => true
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '2FA activado correctamente',
                    'redirect' => route('settings')
                ]);
            }
            
            return redirect()->route('settings')->with('status', '2FA activado');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Código incorrecto',
                'errors' => ['code' => ['Código incorrecto']]
            ], 422);
        }
        
        return back()->withErrors(['code' => 'Código incorrecto']);
    }

    public function disable(Request $request)
    {
        $userId = Auth::id();
        DB::table('users')->where('id', $userId)->update([
            'two_factor_enabled' => false, 
            'two_factor_secret' => null
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => '2FA desactivado correctamente',
                'redirect' => route('settings')
            ]);
        }
        
        return redirect()->route('settings')->with('status', '2FA desactivado');
    }
}
