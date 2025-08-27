<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagedPage extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'page_id',
        'name',
        'access_token',
        'token_secret',
        'refresh_token',
        'token_expires_at',
        'category',
        'picture_url',
        'metadata'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'token_expires_at' => 'datetime',
        'metadata' => 'array'
    ];

    protected $hidden = [
        'access_token',
        'token_secret',
        'refresh_token'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
