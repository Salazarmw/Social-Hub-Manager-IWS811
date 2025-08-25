<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OAuthController extends Controller
{
    // Proveedores soportados con sus configuraciones
    private $providers = [
        'x' => [
            'name' => 'X (Twitter)',
            'oauth_version' => '1.0a',
            'manual' => true
        ],
        'google' => [
            'name' => 'Google',
            'oauth_version' => '2.0',
            'socialite' => true,
            'scopes' => ['openid', 'profile', 'email']
        ],
        'github' => [
            'name' => 'GitHub',
            'oauth_version' => '2.0',
            'socialite' => true,
            'scopes' => ['user:email']
        ],
        'discord' => [
            'name' => 'Discord',
            'oauth_version' => '2.0',
            'manual' => true
        ],
        'reddit' => [
            'name' => 'Reddit',
            'oauth_version' => '2.0',
            'manual' => true
        ],
        'telegram' => [
            'name' => 'Telegram',
            'oauth_version' => 'widget',
            'manual' => true
        ]
    ];

    /**
     * Redirige al usuario al proveedor OAuth.
     */
    public function redirect($provider)
    {
        if (!isset($this->providers[$provider])) {
            return back()->with('error', 'Este proveedor no está soportado.');
        }

        switch ($provider) {
            case 'x':
                return $this->redirectX();
            case 'discord':
                return $this->redirectDiscord();
            case 'reddit':
                return $this->redirectReddit();
            case 'telegram':
                return $this->redirectTelegram();
            default:
                // Socialite providers (Google, GitHub)
                if ($this->providers[$provider]['socialite'] ?? false) {
                    return $this->redirectSocialite($provider);
                }
                return back()->with('error', 'Método de autenticación no implementado para este proveedor.');
        }
    }

    /**
     * Maneja el callback del proveedor.
     */
    public function callback($provider)
    {
        try {
            switch ($provider) {
                case 'x':
                    return $this->callbackX();
                case 'discord':
                    return $this->callbackDiscord();
                case 'reddit':
                    return $this->callbackReddit();
                case 'telegram':
                    return $this->callbackTelegram();
                default:
                    if ($this->providers[$provider]['socialite'] ?? false) {
                        return $this->callbackSocialite($provider);
                    }
                    throw new \Exception('Método de callback no implementado');
            }
        } catch (\Exception $e) {
            Log::error("Error en callback de {$provider}: " . $e->getMessage());
            return redirect()->route('settings')->with('error', 'Error al conectar con ' . ucfirst($provider) . ': ' . $e->getMessage());
        }
    }

    // ==================== X (Twitter) ==================== //
    private function redirectX()
    {
        $connection = new \Abraham\TwitterOAuth\TwitterOAuth(
            config('services.x.key'),
            config('services.x.secret')
        );

        $request_token = $connection->oauth('oauth/request_token', [
            'oauth_callback' => route('oauth.callback', 'x')
        ]);

        session([
            'x_oauth_token' => $request_token['oauth_token'],
            'x_oauth_token_secret' => $request_token['oauth_token_secret']
        ]);

        $url = $connection->url('oauth/authorize', [
            'oauth_token' => $request_token['oauth_token']
        ]);

        return redirect($url);
    }

    private function callbackX()
    {
        $oauth_token = request()->get('oauth_token');
        $oauth_verifier = request()->get('oauth_verifier');
        $request_token = session('x_oauth_token');
        $request_token_secret = session('x_oauth_token_secret');

        $connection = new \Abraham\TwitterOAuth\TwitterOAuth(
            config('services.x.key'),
            config('services.x.secret'),
            $request_token,
            $request_token_secret
        );

        $access_token = $connection->oauth('oauth/access_token', [
            'oauth_verifier' => $oauth_verifier
        ]);

        auth()->user()->socialAccounts()->updateOrCreate(
            [
                'provider' => 'x',
                'user_id' => auth()->id()
            ],
            [
                'provider_user_id' => (string)$access_token['user_id'],
                'token' => encrypt($access_token['oauth_token']),
                'token_secret' => encrypt($access_token['oauth_token_secret']),
                'nickname' => $access_token['screen_name'],
            ]
        );

        return redirect()->route('settings')->with('status', 'X conectado correctamente.');
    }

    // ==================== Discord ==================== //
    private function redirectDiscord()
    {
        $params = [
            'client_id' => env('DISCORD_CLIENT_ID'),
            'redirect_uri' => route('oauth.callback', 'discord'),
            'response_type' => 'code',
            'scope' => 'identify email'
        ];

        $url = 'https://discord.com/api/oauth2/authorize?' . http_build_query($params);
        return redirect($url);
    }

    private function callbackDiscord()
    {
        $code = request()->get('code');

        // Intercambiar código por token
        $response = Http::asForm()->post('https://discord.com/api/oauth2/token', [
            'client_id' => env('DISCORD_CLIENT_ID'),
            'client_secret' => env('DISCORD_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => route('oauth.callback', 'discord'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al obtener token de Discord');
        }

        $tokenData = $response->json();

        // Obtener información del usuario
        $userResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenData['access_token']
        ])->get('https://discord.com/api/users/@me');

        if (!$userResponse->successful()) {
            throw new \Exception('Error al obtener datos del usuario de Discord');
        }

        $userData = $userResponse->json();

        auth()->user()->socialAccounts()->updateOrCreate(
            ['provider' => 'discord'],
            [
                'provider_user_id' => $userData['id'],
                'nickname' => $userData['username'],
                'avatar' => $userData['avatar'] ? "https://cdn.discordapp.com/avatars/{$userData['id']}/{$userData['avatar']}.png" : null,
                'token' => encrypt($tokenData['access_token']),
                'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : null,
                'expires_in' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
            ]
        );

        return redirect()->route('settings')->with('status', 'Discord conectado correctamente.');
    }

    // ==================== Reddit ==================== //
    private function redirectReddit()
    {
        $state = str()->random(32);
        session(['reddit_state' => $state]);

        $params = [
            'client_id' => config('services.reddit.client_id'),
            'response_type' => 'code',
            'state' => $state,
            'redirect_uri' => config('services.reddit.redirect_uri'),
            'duration' => 'permanent',
            'scope' => 'identity edit submit read'
        ];

        $url = 'https://www.reddit.com/api/v1/authorize?' . http_build_query($params);
        return redirect($url);
    }

    private function callbackReddit()
    {
        $code = request()->get('code');
        $state = request()->get('state');

        if ($state !== session('reddit_state')) {
            throw new \Exception('Estado inválido en callback de Reddit');
        }

        // Intercambiar código por token
        $response = Http::withBasicAuth(config('services.reddit.client_id'), config('services.reddit.client_secret'))
            ->asForm()
            ->withHeaders(['User-Agent' => config('services.reddit.user_agent')])
            ->post('https://www.reddit.com/api/v1/access_token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.reddit.redirect_uri'),
            ]);

        if (!$response->successful()) {
            throw new \Exception('Error al obtener token de Reddit');
        }

        $tokenData = $response->json();

        // Obtener información del usuario
        $userResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
            'User-Agent' => env('APP_NAME') . '/1.0'
        ])->get('https://oauth.reddit.com/api/v1/me');

        if (!$userResponse->successful()) {
            throw new \Exception('Error al obtener datos del usuario de Reddit');
        }

        $userData = $userResponse->json();

        auth()->user()->socialAccounts()->updateOrCreate(
            ['provider' => 'reddit'],
            [
                'provider_user_id' => $userData['id'],
                'nickname' => $userData['name'],
                'token' => encrypt($tokenData['access_token']),
                'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : null,
                'expires_in' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
            ]
        );

        return redirect()->route('settings')->with('status', 'Reddit conectado correctamente.');
    }

    // ==================== Telegram ==================== //
    private function redirectTelegram()
    {
        // Telegram usa un widget especial, redirigir a una página con instrucciones
        return redirect()->route('settings')->with('info', 'Para conectar Telegram, usa el widget en la configuración.');
    }

    private function callbackTelegram()
    {
        // Validar datos del widget de Telegram
        $telegramData = request()->only(['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash']);

        if (!$this->validateTelegramAuth($telegramData)) {
            throw new \Exception('Datos de autenticación de Telegram inválidos');
        }

        auth()->user()->socialAccounts()->updateOrCreate(
            ['provider' => 'telegram'],
            [
                'provider_user_id' => $telegramData['id'],
                'nickname' => $telegramData['username'] ?? ($telegramData['first_name'] . ' ' . ($telegramData['last_name'] ?? '')),
                'avatar' => $telegramData['photo_url'] ?? null,
                'token' => encrypt($telegramData['id']), // Telegram no usa tokens tradicionales
            ]
        );

        return redirect()->route('settings')->with('status', 'Telegram conectado correctamente.');
    }

    // ==================== Socialite (Google, GitHub) ==================== //
    private function redirectSocialite($provider)
    {
        $redirector = Socialite::driver($provider);

        if (isset($this->providers[$provider]['scopes'])) {
            $redirector->scopes($this->providers[$provider]['scopes']);
        }

        return $redirector->redirect();
    }

    private function callbackSocialite($provider)
    {
        $socialiteUser = Socialite::driver($provider)->user();

        auth()->user()->socialAccounts()->updateOrCreate(
            ['provider' => $provider],
            [
                'provider_user_id' => $socialiteUser->getId(),
                'nickname' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
                'avatar' => $socialiteUser->getAvatar(),
                'token' => encrypt($socialiteUser->token),
                'refresh_token' => $socialiteUser->refreshToken ? encrypt($socialiteUser->refreshToken) : null,
                'expires_in' => isset($socialiteUser->expiresIn) ? now()->addSeconds($socialiteUser->expiresIn) : null,
            ]
        );

        return redirect()->route('settings')->with('status', ucfirst($provider) . ' conectado correctamente.');
    }

    // ==================== Utilidades ==================== //
    private function validateTelegramAuth($data)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $hash = $data['hash'];
        unset($data['hash']);

        $dataCheckString = [];
        foreach ($data as $key => $value) {
            $dataCheckString[] = $key . '=' . $value;
        }
        sort($dataCheckString);
        $dataCheckString = implode("\n", $dataCheckString);

        $secretKey = hash('sha256', $botToken, true);
        $hashCheck = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($hash, $hashCheck);
    }

    /**
     * Obtiene la lista de proveedores disponibles
     */
    public function getAvailableProviders()
    {
        return $this->providers;
    }

    /**
     * Desconecta una cuenta social.
     */
    public function revoke($provider)
    {
        if (!isset($this->providers[$provider])) {
            return back()->withErrors('Proveedor no válido.');
        }

        auth()->user()->socialAccounts()->where('provider', $provider)->delete();
        auth()->user()->managedPages()->where('provider', $provider)->delete();

        return back()->with('status', ucfirst($provider) . ' desconectado.');
    }
}
