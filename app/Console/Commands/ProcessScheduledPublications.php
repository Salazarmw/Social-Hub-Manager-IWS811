<?php

namespace App\Console\Commands;

use App\Jobs\ProcessScheduledPublications as ProcessScheduledPublicationsJob;
use Illuminate\Console\Command;

class ProcessScheduledPublications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:process-publications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesar horarios de publicación y crear publicaciones programadas automáticamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando procesamiento de horarios de publicación...');
        
        // Dispatch del job
        ProcessScheduledPublicationsJob::dispatch();
        
        $this->info('Job de procesamiento de horarios enviado a la cola.');
        
        return 0;
    }
}
