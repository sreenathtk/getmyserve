<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallAgent extends Model
{
    protected $fillable = [
        'user_id', 'ziwo_agent_id', 'ziwo_extension',
        'ziwo_username', 'display_name',
        'status', 'last_status_changed_at',
        'total_calls_today', 'total_talk_seconds_today', 'is_active',
    ];

    protected $casts = [
        'last_status_changed_at' => 'datetime',
        'is_active'              => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'agent_id');
    }

    public function isOnline(): bool
    {
        return in_array($this->status, ['online', 'busy', 'on_call']);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'online'  => 'badge-success',
            'on_call' => 'badge-primary',
            'busy'    => 'badge-warning',
            'paused'  => 'badge-secondary',
            default   => 'badge-danger',
        };
    }
}
