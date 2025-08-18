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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // facebook, linkedin, etc.
            $table->string('provider_user_id')->unique(); // ID único del usuario en el proveedor
            $table->text('token'); // token encriptado
            $table->text('token_secret')->nullable(); // para X (Twitter)
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_in')->nullable();
            $table->string('nickname')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'provider']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
