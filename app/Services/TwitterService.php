<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Log;

class TwitterService implements SocialServiceInterface
{
    protected $connection;

    public function __construct()
    {
        $this->connection = new TwitterOAuth(
            config('services.x.key'),
            config('services.x.secret')
        );
    }

    public function publish(string $content, SocialAccount $account): bool
    {
        try {
            // Configurar tokens de acceso del usuario
            $this->connection->setOauthToken(
                decrypt($account->token),
                decrypt($account->token_secret)
            );

            // Configurar para la API v2
            $this->connection->setApiVersion('2');

            // Intentar publicar el tweet usando la API v2
            $result = $this->connection->post("tweets", [
                "text" => $content
            ]);

            // Verificar si hubo error
            $statusCode = $this->connection->getLastHttpCode();
            if ($statusCode != 201) {
                Log::error("Error al publicar en Twitter. Código: " . $statusCode . " Respuesta: " . json_encode($result));
                return false;
            }

            Log::info("Tweet publicado exitosamente: {$content}");
            return true;
        } catch (\Exception $e) {
            Log::error("Excepción al publicar en Twitter: " . $e->getMessage());
            return false;
        }
    }
}
