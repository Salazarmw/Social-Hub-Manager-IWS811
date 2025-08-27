<?php

namespace App\Jobs;

use App\Models\PublishingSchedule;
use App\Models\ScheduledPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessScheduledPublications implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();

        Log::info('Procesando horarios de publicación automatizados', ['timestamp' => $now]);

        // Obtener horarios activos que necesitan generar publicaciones
        $schedules = PublishingSchedule::active()->get();

        foreach ($schedules as $schedule) {
            $this->processSchedule($schedule, $now);
        }

        Log::info('Finalizó el procesamiento de horarios', ['processed_schedules' => $schedules->count()]);
    }

    /**
     * Procesar un horario específico
     */
    private function processSchedule(PublishingSchedule $schedule, Carbon $now)
    {
        if ($schedule->is_recurring) {
            $this->processRecurringSchedule($schedule, $now);
        } else {
            $this->processSpecificSchedule($schedule, $now);
        }
    }

    /**
     * Procesar un horario recurrente
     */
    private function processRecurringSchedule(PublishingSchedule $schedule, Carbon $now)
    {
        $currentDayOfWeek = $now->dayOfWeek;

        if (!in_array($currentDayOfWeek, $schedule->recurring_days ?? [])) {
            return;
        }

        if ($schedule->recurring_start_date && $now->lt($schedule->recurring_start_date)) {
            return;
        }

        if ($schedule->recurring_end_date && $now->gt($schedule->recurring_end_date)) {
            $schedule->update(['status' => 'completed']);
            return;
        }

        // Crear la fecha programada para hoy a la hora especificada
        $scheduledDateTime = $now->copy()
            ->setTimeFromTimeString($schedule->recurring_time->format('H:i:s'));

        // Si ya pasó la hora de hoy, programar para mañana
        if ($now->gt($scheduledDateTime)) {
            $scheduledDateTime->addDay();
        }

        // Verificar si ya existe una publicación programada para esta fecha/hora
        $existingPost = ScheduledPost::where('user_id', $schedule->user_id)
            ->whereDate('scheduled_date', $scheduledDateTime->toDateString())
            ->whereTime('scheduled_date', $scheduledDateTime->format('H:i:s'))
            ->first();

        if (!$existingPost) {
            ScheduledPost::create([
                'user_id' => $schedule->user_id,
                'content' => $schedule->content,
                'platforms' => $schedule->platforms,
                'scheduled_date' => $scheduledDateTime,
                'status' => 'pending',
            ]);

            Log::info('Publicación recurrente creada', [
                'schedule_id' => $schedule->id,
                'scheduled_for' => $scheduledDateTime,
                'user_id' => $schedule->user_id
            ]);
    }

    /**
     * Procesar un horario específico
     */
    private function processSpecificSchedule(PublishingSchedule $schedule, Carbon $now)
    {
        // Obtener la fecha y hora programada
        $scheduleDateTime = $schedule->schedule_date->copy()
            ->setTimeFromTimeString($schedule->schedule_time->format('H:i:s'));

        // Si la fecha ya pasó, marcar como completado
        if ($now->gt($scheduleDateTime)) {
            $schedule->update(['status' => 'completed']);
            return;
        }

        // Verificar si ya existe una publicación programada
        $existingPost = ScheduledPost::where('user_id', $schedule->user_id)
            ->whereDate('scheduled_date', $scheduleDateTime->toDateString())
            ->whereTime('scheduled_date', $scheduleDateTime->format('H:i:s'))
            ->first();

        if (!$existingPost) {
            ScheduledPost::create([
                'user_id' => $schedule->user_id,
                'content' => $schedule->content,
                'platforms' => $schedule->platforms,
                'scheduled_date' => $scheduleDateTime,
                'status' => 'pending',
            ]);

            $schedule->update(['status' => 'completed']);

            Log::info('Publicación específica creada', [
                'schedule_id' => $schedule->id,
                'scheduled_for' => $scheduleDateTime,
                'user_id' => $schedule->user_id
            ]);
    }
}
