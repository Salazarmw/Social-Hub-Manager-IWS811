<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $providerUser = Socialite::driver($provider)->user();
        auth()->user()->socialAccounts()->updateOrCreate(
            ['provider' => $provider],
            ['token' => encrypt($providerUser->token), 'nickname' => $providerUser->nickname ?? null]
        );
        return redirect()->route('settings')->with('status', ucfirst($provider) . ' conectado');
    }

    public function revoke($provider)
    {
        auth()->user()->socialAccounts()->where('provider', $provider)->delete();
        return back()->with('status', ucfirst($provider) . ' desconectado');
    }
}
