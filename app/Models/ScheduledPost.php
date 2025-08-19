<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledPost extends Model
{
    use HasFactory;
    
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content',
        'scheduled_date',
        'platforms',
        'status',
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
        'scheduled_date' => 'datetime',
        'published_at' => 'datetime',
    ];
    
    /**
     * Obtener el usuario al que pertenece esta publicación programada.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener publicaciones pendientes.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
