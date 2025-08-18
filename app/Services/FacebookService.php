<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FacebookService
{
    public function publish(string $content): bool
    {
        // Lógica real para publicar en Facebook
        Log::info("Publicando en Facebook: {$content}");
        return true; // Simulación
    }
}