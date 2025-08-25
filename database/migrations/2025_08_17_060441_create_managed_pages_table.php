<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('managed_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // facebook, instagram, twitter, reddit, etc.
            $table->string('page_id'); // ID de la página/perfil/subreddit
            $table->string('name');
            $table->text('access_token')->nullable(); // Token de acceso encriptado
            $table->text('token_secret')->nullable(); // Para OAuth 1.0a (Twitter)
            $table->text('refresh_token')->nullable(); // Para OAuth 2.0
            $table->timestamp('token_expires_at')->nullable(); // Expiración del token
            $table->string('category')->nullable();
            $table->string('picture_url')->nullable();
            $table->json('metadata')->nullable(); // Datos adicionales específicos del proveedor
            $table->timestamps();
            
            // Índice compuesto para evitar duplicados de página/perfil por usuario y proveedor
            $table->unique(['user_id', 'provider', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managed_pages');
    }
};
