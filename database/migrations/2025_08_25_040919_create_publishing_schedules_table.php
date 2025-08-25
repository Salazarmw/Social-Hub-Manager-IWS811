<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publishing_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->json('platforms');
            $table->date('schedule_date')->nullable();
            $table->time('schedule_time')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled', 'failed'])->default('active');
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_days')->nullable();
            $table->time('recurring_time')->nullable();
            $table->date('recurring_start_date')->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index(['user_id', 'schedule_date', 'schedule_time']);
            $table->index(['status', 'schedule_date']);
            $table->index(['is_recurring']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publishing_schedules');
    }
};
