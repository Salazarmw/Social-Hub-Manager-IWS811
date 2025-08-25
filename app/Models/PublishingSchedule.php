<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PublishingSchedule extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'platforms',
        'schedule_date',
        'schedule_time',
        'status',
        'is_recurring',
        'recurring_days',
        'recurring_time',
        'recurring_start_date',
        'recurring_end_date',
        'notes',
        'published_at',
        'error_message',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'platforms' => 'json',
        'schedule_date' => 'date',
        'schedule_time' => 'datetime:H:i',
        'is_recurring' => 'boolean',
        'recurring_days' => 'json',
        'recurring_time' => 'datetime:H:i',
        'recurring_start_date' => 'date',
        'recurring_end_date' => 'date',
        'published_at' => 'datetime',
    ];

    /**
     * Obtener el usuario al que pertenece esta programación.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener horarios activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para obtener horarios recurrentes.
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Scope para obtener horarios de una fecha específica.
     */
    public function scopeForDate($query, $date)
    {
        $date = Carbon::parse($date);

        return $query->where(function ($query) use ($date) {
            // Horarios específicos para esta fecha
            $query->where(function ($q) use ($date) {
                $q->where('is_recurring', false)
                    ->whereDate('schedule_date', $date);
            })
                // O horarios recurrentes que aplican a este día de la semana
                ->orWhere(function ($q) use ($date) {
                    $dayOfWeek = $date->dayOfWeek; // 0=domingo, 1=lunes, etc.
                    $q->where('is_recurring', true)
                        ->whereJsonContains('recurring_days', $dayOfWeek)
                        ->where(function ($subQ) use ($date) {
                            $subQ->whereNull('recurring_start_date')
                                ->orWhereDate('recurring_start_date', '<=', $date);
                        })
                        ->where(function ($subQ) use ($date) {
                            $subQ->whereNull('recurring_end_date')
                                ->orWhereDate('recurring_end_date', '>=', $date);
                        });
                });
        });
    }

    /**
     * Obtener el texto legible para los días recurrentes.
     */
    public function getRecurringDaysTextAttribute()
    {
        if (!$this->is_recurring || !$this->recurring_days) {
            return null;
        }

        $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $selectedDays = [];

        foreach ($this->recurring_days as $dayNum) {
            $selectedDays[] = $days[$dayNum] ?? '';
        }

        return implode(', ', $selectedDays);
    }

    /**
     * Verificar si la programación está activa para una fecha específica.
     */
    public function isActiveForDate($date)
    {
        $date = Carbon::parse($date);

        if (!$this->is_recurring) {
            return $this->schedule_date->isSameDay($date);
        }

        // Verificar si el día de la semana coincide
        $dayOfWeek = $date->dayOfWeek;
        if (!in_array($dayOfWeek, $this->recurring_days ?? [])) {
            return false;
        }

        // Verificar rango de fechas si están definidas
        if ($this->recurring_start_date && $date->lt($this->recurring_start_date)) {
            return false;
        }

        if ($this->recurring_end_date && $date->gt($this->recurring_end_date)) {
            return false;
        }

        return true;
    }
}
