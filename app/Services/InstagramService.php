<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class InstagramService
{
    public function publish(string $content): bool
    {
        // Lógica real para publicar en Instagram
        Log::info("Publicando en Instagram: {$content}");
        return true; // Simulación
    }
}