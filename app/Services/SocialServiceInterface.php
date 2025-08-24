<?php

namespace App\Services;

use App\Models\SocialAccount;

interface SocialServiceInterface
{
    /**
     * Publica contenido en la red social.
     *
     * @param string $content El contenido a publicar
     * @param SocialAccount $account La cuenta social a usar para publicar
     * @return bool true si la publicación fue exitosa, false en caso contrario
     */
    public function publish(string $content, SocialAccount $account): bool;
}
