<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show()
    { /* returns QR code view – omitted for brevity */
    }

    public function enable(Request $r)
    {
        $secret = decrypt(session('2fa_secret'));
        if (Google2FA::verifyKey($secret, $r->code)) {
            auth()->user()->update(['two_factor_secret' => encrypt($secret), 'two_factor_enabled' => true]);
            return redirect()->route('settings')->with('status', '2FA activado');
        }
        return back()->withErrors(['code' => 'Código incorrecto']);
    }

    public function disable(Request $r)
    {
        auth()->user()->update(['two_factor_enabled' => false, 'two_factor_secret' => null]);
        return back()->with('status', '2FA desactivado');
    }
}
