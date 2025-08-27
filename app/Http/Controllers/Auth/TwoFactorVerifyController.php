<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorVerifyController extends Controller
{
    public function show()
    {
        return view('auth.twofactor');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = auth()->user();
        $g2fa = new Google2FA();
        if ($user->two_factor_secret && $g2fa->verifyKey(decrypt($user->two_factor_secret), $request->code)) {
            $request->session()->put('2fa_passed', true);
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['code' => 'Código incorrecto']);
    }
}
