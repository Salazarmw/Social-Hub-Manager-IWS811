<?php

namespace App\Http\Controllers;

use App\Models\PublishingSchedule;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Mostrar la vista del calendario de horarios.
     */
    public function index(Request $request)
    {
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);

        $startOfMonth = Carbon::create($currentYear, $currentMonth, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        // Obtener horarios programados para este mes (incluyendo recurrentes)
        $schedules = PublishingSchedule::where('user_id', Auth::id())
            ->active()
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('is_recurring', false)
                        ->whereBetween('schedule_date', [$startOfMonth, $endOfMonth]);
                })
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('is_recurring', true)
                            ->where(function ($subQ) use ($endOfMonth) {
                                $subQ->whereNull('recurring_start_date')
                                    ->orWhereDate('recurring_start_date', '<=', $endOfMonth);
                            })
                            ->where(function ($subQ) use ($startOfMonth) {
                                $subQ->whereNull('recurring_end_date')
                                    ->orWhereDate('recurring_end_date', '>=', $startOfMonth);
                            });
                    });
            })
            ->orderBy('schedule_time')
            ->get();

        $scheduledPosts = ScheduledPost::where('user_id', Auth::id())
            ->whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['pending', 'processing'])
            ->get();

        return view('calendar.index', compact('schedules', 'scheduledPosts', 'currentMonth', 'currentYear'));
    }

    /**
     * Obtener eventos del calendario para AJAX.
     */
    public function getEvents(Request $request)
    {
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));

        $events = [];

        // Obtener solo los horarios programados que no son automáticos
        $schedules = PublishingSchedule::where('user_id', Auth::id())
            ->active()
            ->where('status', '!=', 'completed')
            ->get();

        // Procesar cada horario para generar eventos en el período solicitado
        foreach ($schedules as $schedule) {
            if ($schedule->is_recurring) {
                $current = $start->copy();
                while ($current->lte($end)) {
                    if ($schedule->isActiveForDate($current)) {
                        $eventDateTime = Carbon::createFromFormat(
                            'Y-m-d H:i:s',
                            $current->format('Y-m-d') . ' ' . $schedule->recurring_time->format('H:i:s'),
                            config('app.timezone')
                        );

                        $events[] = [
                            'id' => 'schedule_' . $schedule->id . '_' . $current->format('Y-m-d'),
                            'title' => $schedule->title,
                            'start' => $eventDateTime->format('Y-m-d\TH:i:s'),  // Formato ISO sin zona horaria
                            'type' => 'schedule',
                            'schedule_id' => $schedule->id,
                            'className' => 'schedule-event recurring-event',
                            'backgroundColor' => '#3B82F6',
                            'borderColor' => '#1D4ED8',
                            'extendedProps' => [
                                'content' => $schedule->content,
                                'isRecurring' => true,
                                'notes' => $schedule->notes,
                                'platforms' => $schedule->platforms,
                                'type' => 'schedule',
                                'schedule_id' => $schedule->id
                            ]
                        ];
                    }
                    $current->addDay();
                }
            } else {
                if ($schedule->schedule_date && $schedule->schedule_date->between($start, $end)) {
                    $eventDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->schedule_time->format('H:i:s'),
                        config('app.timezone')
                    );

                    $events[] = [
                        'id' => 'schedule_' . $schedule->id,
                        'title' => $schedule->title,
                        'start' => $eventDateTime->format('Y-m-d\TH:i:s'),
                        'type' => 'schedule',
                        'schedule_id' => $schedule->id,
                        'className' => 'schedule-event specific-event',
                        'backgroundColor' => '#059669',
                        'borderColor' => '#047857',
                        'extendedProps' => [
                            'content' => $schedule->content,
                            'isRecurring' => false,
                            'notes' => $schedule->notes,
                            'platforms' => $schedule->platforms,
                            'type' => 'schedule',
                            'schedule_id' => $schedule->id
                        ]
                    ];
                }
            }
        }

        // Ya no mostraremos los ScheduledPosts en el calendario

        return response()->json($events);
    }

    /**
     * Mostrar el formulario para crear un nuevo horario.
     */
    public function create(Request $request)
    {
        $selectedDate = $request->get('date');
        $selectedTime = $request->get('time', '09:00');

        $connectedAccounts = SocialAccount::where('user_id', Auth::id())->get();
        $availablePlatforms = [];

        foreach ($connectedAccounts as $account) {
            $availablePlatforms[] = [
                'value' => $account->provider,
                'name' => ucfirst($account->provider),
                'icon' => $account->provider . '.svg'
            ];
        }

        if (empty($availablePlatforms)) {
            $availablePlatforms = [
                ['value' => 'x', 'name' => 'X (Twitter)', 'icon' => 'x.svg'],
                ['value' => 'reddit', 'name' => 'Reddit', 'icon' => 'reddit.svg'],
            ];
        }

        return view('calendar.create', compact('selectedDate', 'selectedTime', 'availablePlatforms', 'connectedAccounts'));
    }

    /**
     * Almacenar un nuevo horario.
     */
    public function store(Request $request)
    {
        // Debug: Log el request completo
        Log::info('Request recibido en store:', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:twitter,reddit,x',
            'is_recurring' => 'boolean',
            'schedule_date' => 'required_if:is_recurring,false|nullable|date|after_or_equal:today',
            'schedule_time' => 'required_if:is_recurring,false|nullable|date_format:H:i',
            'recurring_days' => 'required_if:is_recurring,true|nullable|array|min:1',
            'recurring_days.*' => 'integer|between:0,6',
            'recurring_time' => 'required_if:is_recurring,true|nullable|date_format:H:i',
            'recurring_start_date' => 'nullable|date|after_or_equal:today',
            'recurring_end_date' => 'nullable|date|after:recurring_start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Debug: Log los datos antes de crear
        Log::info('Datos para crear horario:', [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'platforms' => $request->platforms,
            'is_recurring' => $request->boolean('is_recurring'),
            'schedule_date' => $request->boolean('is_recurring') ? null : $request->schedule_date,
            'schedule_time' => $request->boolean('is_recurring') ? null : $request->schedule_time,
            'recurring_days' => $request->boolean('is_recurring') ? $request->recurring_days : null,
            'recurring_time' => $request->boolean('is_recurring') ? $request->recurring_time : null,
            'recurring_start_date' => $request->recurring_start_date,
            'recurring_end_date' => $request->recurring_end_date,
            'notes' => $request->notes,
        ]);

        try {
            $schedule = PublishingSchedule::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content' => $request->content,
                'platforms' => $request->platforms,
                'is_recurring' => $request->boolean('is_recurring'),
                'schedule_date' => $request->boolean('is_recurring') ? null : $request->schedule_date,
                'schedule_time' => $request->boolean('is_recurring') ? null : $request->schedule_time,
                'recurring_days' => $request->boolean('is_recurring') ? $request->recurring_days : null,
                'recurring_time' => $request->boolean('is_recurring') ? $request->recurring_time : null,
                'recurring_start_date' => $request->recurring_start_date,
                'recurring_end_date' => $request->recurring_end_date,
                'notes' => $request->notes,
            ]);

            // Si no es recurrente, crear inmediatamente el ScheduledPost
            if (!$request->boolean('is_recurring')) {
                $scheduleDateTime = Carbon::parse($request->schedule_date)
                    ->setTimeFromTimeString($request->schedule_time);

                // Convertir 'x' a 'twitter' en las plataformas
                $platforms = array_map(function($platform) {
                    return $platform === 'x' ? 'twitter' : $platform;
                }, $request->platforms);

                ScheduledPost::create([
                    'user_id' => Auth::id(),
                    'content' => $request->content,
                    'platforms' => $platforms,
                    'scheduled_date' => $scheduleDateTime,
                    'status' => 'pending',
                ]);

                Log::info('Post programado creado inmediatamente', [
                    'schedule_id' => $schedule->id,
                    'scheduled_for' => $scheduleDateTime
                ]);
            }

            Log::info('Horario creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear horario:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('calendar.index')
                ->with('error', 'Error al crear el horario: ' . $e->getMessage());
        }

        return redirect()->route('calendar.index')
            ->with('success', 'Horario de publicación creado correctamente');
    }

    /**
     * Mostrar un horario específico.
     */
    public function show($id)
    {
        $schedule = PublishingSchedule::where('user_id', Auth::id())->findOrFail($id);
        return view('calendar.show', compact('schedule'));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit($id)
    {
        $schedule = PublishingSchedule::where('user_id', Auth::id())->findOrFail($id);

        $connectedAccounts = SocialAccount::where('user_id', Auth::id())->get();
        $availablePlatforms = [];

        foreach ($connectedAccounts as $account) {
            $availablePlatforms[] = [
                'value' => $account->provider,
                'name' => ucfirst($account->provider),
                'icon' => $account->provider . '.svg'
            ];
        }

        if (empty($availablePlatforms)) {
            $availablePlatforms = [
                ['value' => 'x', 'name' => 'X (Twitter)', 'icon' => 'x.svg'],
                ['value' => 'reddit', 'name' => 'Reddit', 'icon' => 'reddit.svg'],
            ];
        }

        return view('calendar.edit', compact('schedule', 'availablePlatforms', 'connectedAccounts'));
    }

    /**
     * Actualizar un horario.
     */
    public function update(Request $request, $id)
    {
        $schedule = PublishingSchedule::where('user_id', Auth::id())->findOrFail($id);

        // Debug: Log el request de actualización
        Log::info('Request recibido en update:', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:twitter,reddit,x',
            'is_recurring' => 'boolean',
            'schedule_date' => 'required_if:is_recurring,false|nullable|date|after_or_equal:today',
            'schedule_time' => 'required_if:is_recurring,false|nullable|date_format:H:i',
            'recurring_days' => 'required_if:is_recurring,true|nullable|array|min:1',
            'recurring_days.*' => 'integer|between:0,6',
            'recurring_time' => 'required_if:is_recurring,true|nullable|date_format:H:i',
            'recurring_start_date' => 'nullable|date|after_or_equal:today',
            'recurring_end_date' => 'nullable|date|after:recurring_start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $schedule->update([
                'title' => $request->title,
                'content' => $request->content,
                'platforms' => $request->platforms,
                'is_recurring' => $request->boolean('is_recurring'),
                'schedule_date' => $request->boolean('is_recurring') ? null : $request->schedule_date,
                'schedule_time' => $request->boolean('is_recurring') ? null : $request->schedule_time,
                'recurring_days' => $request->boolean('is_recurring') ? $request->recurring_days : null,
                'recurring_time' => $request->boolean('is_recurring') ? $request->recurring_time : null,
                'recurring_start_date' => $request->recurring_start_date,
                'recurring_end_date' => $request->recurring_end_date,
                'notes' => $request->notes,
            ]);

            Log::info('Horario actualizado exitosamente:', ['id' => $id]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar horario:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('calendar.index')
                ->with('error', 'Error al actualizar el horario: ' . $e->getMessage());
        }

        return redirect()->route('calendar.index')
            ->with('success', 'Horario de publicación actualizado correctamente');
    }

    /**
     * Eliminar un horario.
     */
    public function destroy($id)
    {
        try {
            $schedule = PublishingSchedule::where('user_id', Auth::id())->findOrFail($id);

            Log::info('Eliminando horario:', [
                'id' => $id,
                'title' => $schedule->title,
                'user_id' => Auth::id()
            ]);

            $schedule->delete();

            Log::info('Horario eliminado exitosamente:', ['id' => $id]);

            return redirect()->route('calendar.index')
                ->with('success', 'Horario de publicación eliminado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar horario:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('calendar.index')
                ->with('error', 'Error al eliminar el horario: ' . $e->getMessage());
        }
    }
}
