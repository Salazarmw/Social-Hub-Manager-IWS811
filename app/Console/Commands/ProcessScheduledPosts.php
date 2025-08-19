<?php

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPost;
use App\Models\ScheduledPost;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ProcessScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa las publicaciones programadas cuya fecha sea igual o anterior a la actual.';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Procesando publicaciones programadas a las {$now->format('Y-m-d H:i:s')}");
        
        // Buscar publicaciones pendientes cuya fecha programada sea menor o igual a la actual
        $posts = ScheduledPost::where('status', 'pending')
            ->where('scheduled_date', '<=', $now)
            ->get();
            
        $count = $posts->count();
        $this->info("Se encontraron {$count} publicaciones para procesar");
        
        if ($count === 0) {
            return 0;
        }
        
        // Procesar cada publicación
        foreach ($posts as $post) {
            $this->info("Procesando publicación ID: {$post->id}");
            
            // Despachar el job para procesar la publicación
            PublishScheduledPost::dispatch($post);
        }
        
        $this->info("Se han enviado {$count} publicaciones a la cola de procesamiento");
        
        return 0;
    }
}
