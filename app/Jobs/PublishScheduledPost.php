<?php

namespace App\Jobs;

use App\Models\ScheduledPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\TwitterService;
use App\Services\FacebookService;
use App\Services\InstagramService;

class PublishScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected ScheduledPost $post)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        TwitterService $twitterService,
        FacebookService $facebookService,
        InstagramService $instagramService
    ): void
    {
        try {
            // Actualizar el estado a procesando
            $this->post->update(['status' => 'processing']);
            $this->post->save();
            
            $publishedSuccessfully = true;
            foreach ($this->post->platforms as $platform) {
                try {
                    switch ($platform) {
                        case 'twitter':
                            $twitterService->publish($this->post->content);
                            break;
                        case 'facebook':
                            $facebookService->publish($this->post->content);
                            break;
                        case 'instagram':
                            $instagramService->publish($this->post->content);
                            break;
                        default:
                            Log::warning("Plataforma desconocida: {$platform}");
                            $publishedSuccessfully = false;
                            break;
                    }
                } catch (\Exception $e) {
                    Log::error("Error al publicar en {$platform} para la publicación {$this->post->id}: {$e->getMessage()}");
                    $publishedSuccessfully = false;
                }
            }
            
            if ($publishedSuccessfully) {
                $this->post->update(['status' => 'published', 'published_at' => now()]);
                $this->post->save();
                Log::info("Publicación {$this->post->id} procesada y publicada correctamente");
            } else {
                $this->post->update(['status' => 'failed', 'error_message' => 'Fallo al publicar en una o más plataformas.']);
                $this->post->save();
                Log::warning("Publicación {$this->post->id} procesada con fallos en algunas plataformas.");
            }
            
        } catch (\Exception $e) {
            // En caso de error, actualizar el estado a fallido
            $this->post->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            $this->post->save();
            
            Log::error("Error general al procesar la publicación {$this->post->id}: {$e->getMessage()}");
            
            // Relanzar la excepción para que el job falle y se registre en la cola
            throw $e;
        }
    }
}
