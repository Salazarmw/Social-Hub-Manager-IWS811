<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditService implements SocialServiceInterface
{
    private string $userAgent;

    public function __construct()
    {
        $this->userAgent = config('services.reddit.user_agent');
    }

    public function publish(string $content, SocialAccount $account): bool
    {
        try {
            // Verificar la cuenta autenticada primero
            $meResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . decrypt($account->token),
                'User-Agent' => $this->userAgent,
            ])->get('https://oauth.reddit.com/api/v1/me');
            
            Log::info("Información del usuario autenticado: " . $meResponse->body());
            
            if (!$meResponse->successful()) {
                Log::error("Error al obtener información del usuario: " . $meResponse->body());
                return false;
            }
            
            $userData = $meResponse->json();
            $username = $userData['name'] ?? null;
            
            if (!$username) {
                Log::error("No se pudo obtener el nombre de usuario de Reddit");
                return false;
            }
            
            // Usar el perfil del usuario directamente (formato u_username)
            $subreddit = "u_" . $username;
            
            // Preparar el título (usar las primeras palabras del contenido)
            $title = explode("\n", $content)[0]; // Usar la primera línea como título
            if (strlen($title) > 300) {
                $title = substr($title, 0, 297) . '...';
            }

            Log::info("Intentando publicar en Reddit - Subreddit: " . $subreddit);
            Log::info("Token: " . substr(decrypt($account->token), 0, 10) . '...');
            Log::info("User-Agent: " . $this->userAgent);

            // Preparar la petición con los headers necesarios
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . decrypt($account->token),
                'User-Agent' => $this->userAgent,
            ])->asForm()->post('https://oauth.reddit.com/api/submit', [
                'sr' => $subreddit,
                'kind' => 'self',
                'title' => $title,
                'text' => $content,
                'api_type' => 'json',
                'resubmit' => true,
                'send_replies' => true
            ]);

            // Verificar si hubo error
            if (!$response->successful()) {
                Log::error("Error al publicar en Reddit. Código: " . $response->status());
                Log::error("Respuesta completa: " . $response->body());
                return false;
            }

            $responseData = $response->json();
            Log::info("Respuesta de Reddit: " . json_encode($responseData));

            // Verificar si la respuesta contiene errores específicos de Reddit
            if (isset($responseData['json']['errors']) && !empty($responseData['json']['errors'])) {
                Log::error("Errores de Reddit: " . json_encode($responseData['json']['errors']));
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Excepción al publicar en Reddit: " . $e->getMessage());
            return false;
        }
    }
}
