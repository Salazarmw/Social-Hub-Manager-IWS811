<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\SocialAccount;

class PlatformSelectorModal extends Component
{
    public $socialAccounts;

    public function __construct()
    {
        // Obtener las cuentas sociales del usuario autenticado
        $this->socialAccounts = auth()->check() ? SocialAccount::where('user_id', auth()->id())
            ->get()
            ->map(function ($account) {
                $hasValidToken = false;
                $errorMessage = null;

                try {
                    if ($account->expires_in && now()->isAfter($account->expires_in)) {
                        $errorMessage = 'Token expirado';
                    } else {
                        $hasValidToken = true;
                    }
                } catch (\Exception $e) {
                    $errorMessage = 'Error de autenticación';
                }

                return [
                    'provider' => $account->provider,
                    'nickname' => $account->nickname,
                    'hasValidToken' => $hasValidToken,
                    'errorMessage' => $errorMessage
                ];
            }) : collect([]);
    }

    public function render()
    {
        return view('components.platform-selector-modal');
    }
}
