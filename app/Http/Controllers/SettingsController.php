<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $socialAccounts = $user->socialAccounts()->get();
        $accounts = $socialAccounts->keyBy('provider');

        // 2FA Logic - solo generar QR si no está habilitado
        $twoFactorEnabled = $user->two_factor_enabled;
        $qrCode = null;

        if (!$twoFactorEnabled) {
            $g2fa = new Google2FA();
            $secret = $g2fa->generateSecretKey();
            session(['2fa_secret' => encrypt($secret)]);

            $qrUrl = $g2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);

            $renderer = new ImageRenderer(
                new RendererStyle(192),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($qrUrl);
        }

        return view('settings.index', [
            'accounts' => $accounts,
            'twoFactorEnabled' => $twoFactorEnabled,
            'qrCode' => $qrCode,
        ]);
    }
}
