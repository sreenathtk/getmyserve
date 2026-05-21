<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'source', 'event_type', 'payload', 'headers',
        'signature', 'is_verified', 'is_processed',
        'processed_at', 'attempts', 'last_error', 'idempotency_key',
    ];

    protected $casts = [
        'payload'      => 'array',
        'headers'      => 'array',
        'is_verified'  => 'boolean',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function scopeUnprocessed(Builder $q): Builder
    {
        return $q->where('is_processed', false)
                 ->where('is_verified', true)
                 ->where('attempts', '<', 5);
    }

    public function scopeZiwo(Builder $q): Builder
    {
        return $q->where('source', 'ziwo');
    }
}
